<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AiIntentLibrary;
use App\Services\AiActionManager;
use Illuminate\Database\QueryException;

class AiAgentController extends Controller
{
    private $intentLibrary;
    private $actionManager;

    // SECURITY WHITELISTS
    private $allowedTables = [
        'nobs_transactions', 'nobs_registration', 'loan_applications', 
        'loan_repayment_schedules', 'agent_commissions', 'capital_accounts',
        'capital_account_transactions', 'nobs_user_account_numbers',
        'loan_products', 'loan_fees', 'account_types', 'loan_product_fees'
    ];

    private $forbiddenColumns = [
        'password', 'remember_token', 'api_token', 'secret', 'key', 'auth', 'iv', 'salt'
    ];

    public function __construct()
    {
    }

    public function chat(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $compId = $user->comp_id;
            $this->intentLibrary = new AiIntentLibrary($compId);
            $this->actionManager = new AiActionManager();

            $prompt = $request->input('message');
            $sessionId = $request->input('session_id');
            $model = $request->input('model', 'gemini-3-flash-preview');
            $requestApiKey = $request->input('api_key');
            $userContext = $request->input('user_context') ?: [];

            if (empty($prompt)) {
                return response()->json(['success' => false, 'message' => 'Message is required'], 400);
            }

            $apiKey = $requestApiKey ?: env('GOOGLE_AI_API_KEY');
            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'AI API Key is missing.'], 400);
            }

            $session = $this->getOrCreateSession($user, $sessionId, $model);
            $this->storeMessage($session->id, 'user', $prompt);

            $result = $this->processWithGemini($session, $prompt, $model, $apiKey, $compId, $userContext);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'response' => $result['response'],
                'ui_type' => $result['ui_type'],
                'ui_metadata' => $result['ui_metadata']
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => "I encountered an issue: " . $e->getMessage()], 500);
        }
    }

    private function getOrCreateSession($user, $sessionId, $model)
    {
        if ($sessionId) {
            $sess = DB::table('ai_chat_sessions')->where('id', $sessionId)->where('user_id', $user->id)->first();
            if ($sess) return $sess;
        }
        return $this->createNewSession($user, $model);
    }

    private function createNewSession($user, $model)
    {
        $id = DB::table('ai_chat_sessions')->insertGetId([
            'user_id' => $user->id,
            'comp_id' => $user->comp_id,
            'model_name' => $model,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return DB::table('ai_chat_sessions')->where('id', $id)->first();
    }

    private function storeMessage($sessionId, $role, $content, $toolCalls = null, $uiType = 'text', $uiMetadata = null)
    {
        $meta = $uiMetadata !== null ? (is_string($uiMetadata) ? $uiMetadata : json_encode($uiMetadata)) : null;
        return DB::table('ai_messages')->insertGetId([
            'session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'tool_calls' => $toolCalls ? (is_string($toolCalls) ? $toolCalls : json_encode($toolCalls)) : null,
            'ui_type' => $uiType,
            'ui_metadata' => $meta,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function processWithGemini($session, $prompt, $model, $apiKey, $compId, $userContext = null)
    {
        $history = $this->getHistory($session->id);
        $tools = $this->getAvailableTools();
        
        $maxTurns = 4; 
        $currentTurn = 0;
        $lastResponse = null;
        $lastToolOutput = null;
        $activeUiType = 'text';

        while ($currentTurn < $maxTurns) {
            $response = $this->callGeminiApi($model, $apiKey, $history, $tools, $compId, $userContext);
            $lastResponse = $response;
            $candidate = $response['candidates'][0]['content'] ?? null;

            if (!$candidate) break;

            $toolCalls = [];
            foreach ($candidate['parts'] as $part) {
                if (isset($part['functionCall'])) $toolCalls[] = $part;
            }

            if (empty($toolCalls)) {
                if (isset($candidate['parts'][0]['text'])) {
                    $this->storeMessage($session->id, 'model', $candidate['parts'][0]['text'], null, $activeUiType, $lastToolOutput);
                }
                break;
            }

            $execution = $this->handleToolCalls($session, $toolCalls, $userContext);
            $toolResults = $execution['results'];
            
            if ($execution['raw_data'] !== null) $lastToolOutput = $execution['raw_data'];
            if ($execution['ui_type'] !== 'text') $activeUiType = $execution['ui_type'];
            
            foreach ($toolResults as $tr) {
                $this->storeMessage($session->id, 'tool', json_encode($tr['functionResponse']['response']), null, $activeUiType, null);
                DB::table('ai_messages')->where('session_id', $session->id)->orderBy('id', 'desc')->limit(1)->update(['tool_call_id' => $tr['functionResponse']['name']]);
            }

            $history[] = $candidate; 
            $history[] = ['role' => 'function', 'parts' => $toolResults];
            $currentTurn++;
        }

        return [
            'response' => $lastResponse,
            'ui_type' => $activeUiType,
            'ui_metadata' => $lastToolOutput
        ];
    }

    private function handleToolCalls($session, $toolCalls, $userContext = null)
    {
        $results = []; $rawData = null; $uiType = 'text';

        foreach ($toolCalls as $call) {
            $func = $call['functionCall'];
            $args = $func['args'] ?? [];
            $name = $func['name'];
            $output = null;

            try {
                if ($name === 'fetch_from_library') {
                    $intent = $args['intent_name'];
                    $params = $args['params'] ?? [];
                    
                    if ($intent === 'TOTAL_DEPOSITS') $output = $this->intentLibrary->getFinancialSummary('Deposit', $params['date'] ?? null, $params['is_total'] ?? false);
                    elseif ($intent === 'TOTAL_WITHDRAWALS') $output = $this->intentLibrary->getFinancialSummary('Withdraw', $params['date'] ?? null, $params['is_total'] ?? false);
                    elseif ($intent === 'CUSTOMER_SEARCH') $output = $this->intentLibrary->searchCustomers($params['term'] ?? '');
                    elseif ($intent === 'LOAN_OVERVIEW') $output = $this->intentLibrary->getLoanOverview();
                    elseif ($intent === 'RECENT_ACTIVITY') $output = $this->intentLibrary->getRecentActivity();
                    elseif ($intent === 'HELP_MENU') $output = $this->intentLibrary->getHelpMenu($userContext['user']['type'] ?? 'Staff');
                    elseif ($intent === 'ACCOUNT_SUMMARY') $output = $this->intentLibrary->getAccountSummary($params['date'] ?? null);
                } 
                elseif ($name === 'execute_analytical_query') {
                    $output = $this->executeSecureSql($args['sql']);
                }

                if ($output) {
                    $uiType = $output['ui_type'];
                    $rawData = $output['ui_metadata'];
                    $results[] = ['functionResponse' => ['name' => $name, 'response' => ['result' => $output['caption'], 'data' => $output['ui_metadata']]]];
                }
            } catch (\Throwable $e) {
                $results[] = ['functionResponse' => ['name' => $name, 'response' => ['error' => $e->getMessage()]]];
            }
        }
        return ['results' => $results, 'raw_data' => $rawData, 'ui_type' => $uiType];
    }

    private function executeSecureSql($sql)
    {
        // 1. HARD SECURITY BLOCK: No Wildcards & No Mutations
        if (preg_match('/(\*|DROP|DELETE|UPDATE|INSERT|TRUNCATE|ALTER|CREATE|REPLACE|UNION)/i', $sql)) {
            throw new \Exception("Security Violation: Wildcards and mutations are strictly prohibited.");
        }

        // 2. Strict PII Column Blacklist
        foreach ($this->forbiddenColumns as $col) {
            if (preg_match("/\b$col\b/i", $sql)) throw new \Exception("Security Violation: Access to system column '$col' is forbidden.");
        }

        // 3. Table Whitelist Verification
        $targetTable = null;
        foreach ($this->allowedTables as $table) {
            if (preg_match("/\b$table\b/i", $sql)) {
                $targetTable = $table;
                break;
            }
        }
        if (!$targetTable) throw new \Exception("Security Violation: Access restricted to verified business tables only.");

        // 4. Surgical Multi-Tenancy (Handles Joins via Table Aliasing)
        $compId = (int)auth('api')->user()->comp_id;
        
        // Find the FIRST table mention to use as the security anchor
        if (stripos($sql, 'WHERE') !== false) {
            $sql = preg_replace('/WHERE/i', "WHERE $targetTable.comp_id = $compId AND ", $sql, 1);
        } else {
            $injection = " WHERE $targetTable.comp_id = $compId ";
            if (preg_match('/(GROUP BY|ORDER BY|LIMIT)/i', $sql, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1];
                $sql = substr($sql, 0, $pos) . $injection . substr($sql, $pos);
            } else {
                $sql .= $injection;
            }
        }

        try {
            Log::info("AI Secure SQL: $sql");
            $results = DB::select($sql);
            return [
                'ui_type' => 'data_table',
                'ui_metadata' => $results,
                'caption' => "Analysis complete. I found " . count($results) . " matching records."
            ];
        } catch (QueryException $qe) {
            throw new \Exception("The generated analysis plan had a syntax error. Please try asking in a simpler way.");
        }
    }

    private function getAvailableTools()
    {
        return [[
            'function_declarations' => [
                [
                    'name' => 'fetch_from_library',
                    'description' => 'Fetches pre-verified reports and help menus.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intent_name' => ['type' => 'string', 'enum' => ['TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'ACCOUNT_SUMMARY', 'CUSTOMER_SEARCH', 'LOAN_OVERVIEW', 'RECENT_ACTIVITY', 'HELP_MENU']],
                            'params' => ['type' => 'object']
                        ],
                        'required' => ['intent_name']
                    ]
                ],
                [
                    'name' => 'execute_analytical_query',
                    'description' => 'Executes a raw SQL SELECT query for custom analysis.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'sql' => ['type' => 'string', 'description' => 'SQL SELECT. IMPORTANT: NEVER use *. Select specific columns. If joining, qualify columns (e.g. table.column). Do NOT add comp_id.']
                        ],
                        'required' => ['sql']
                    ]
                ]
            ]
        ]];
    }

    private function getHistory($sessionId)
    {
        $messages = DB::table('ai_messages')->where('session_id', $sessionId)->orderBy('created_at', 'asc')->limit(20)->get();
        $history = [];
        foreach ($messages as $msg) {
            if ($msg->role === 'user' || $msg->role === 'model') {
                $parts = [];
                if ($msg->content) $parts[] = ['text' => $msg->content];
                if ($msg->tool_calls) {
                    foreach (json_decode($msg->tool_calls, true) as $call) $parts[] = $call;
                }
                $history[] = ['role' => $msg->role, 'parts' => $parts];
            } else if ($msg->role === 'tool') {
                $history[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $msg->tool_call_id, 'response' => json_decode($msg->content, true)]]]];
            }
        }
        return $history;
    }

    private function callGeminiApi($model, $apiKey, $history, $tools, $compId, $userContext = null)
    {
        $validModels = ['gemini-2.5-pro', 'gemini-2.5-flash', 'gemini-3-flash-preview', 'gemini-3.1-pro-preview', 'gemini-1.5-flash', 'gemini-1.5-pro'];
        if (!in_array($model, $validModels)) $model = 'gemini-1.5-flash'; 

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $greetingContext = "You are the SABS Bank AI Assistant.";
        if ($userContext && isset($userContext['user'])) {
            $u = $userContext['user']; $c = $userContext['company'];
            $greetingContext .= " Speaking to " . ($u['title'] ?? '') . " " . ($u['name'] ?? 'User') . " (Role: " . ($u['type'] ?? 'Staff') . ") at " . ($c['name'] ?? 'SABS Bank') . ".";
        }

        $systemInstruction = "$greetingContext 
        Context: Company ID $compId. Server Date " . date('Y-m-d') . ".
        MISSION: Senior Financial Analyst. 0% hallucination. 100% data grounding.
        SCHEMA:
        - `nobs_transactions`: amount, name_of_transaction (Deposit, Withdraw, Loan Repayment), account_number, agentname.
        - `nobs_registration`: first_name, surname, account_number, phone_number.
        - `loan_applications`: status, amount, customer_id.
        - `loan_repayment_schedules`: due_date, total_due, total_paid, status.
        - `loan_products`: name, interest_rate.
        RULES:
        1. FIRST MESSAGE: Warm welcome + call `fetch_from_library(intent_name='HELP_MENU')`.
        2. SQL SECURITY: NEVER use *. Select specific columns. If joining, you MUST qualify columns with table names.
        3. FINANCIAL TRUTH: Exclude transaction descriptions containing 'reversal' unless asked.";

        $payload = ['system_instruction' => ['parts' => [['text' => $systemInstruction]]], 'contents' => $history, 'tools' => $tools, 'generationConfig' => ['temperature' => 0.1, 'topP' => 0.95]];
        $response = Http::post($url, $payload);
        if ($response->failed()) throw new \Exception("Gemini API connection error.");
        return $response->json();
    }
}
