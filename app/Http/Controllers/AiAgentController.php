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

    private $allowedTables = [
        'nobs_transactions', 'nobs_registration', 'loan_applications', 
        'loan_repayment_schedules', 'agent_commissions', 'capital_accounts',
        'capital_account_transactions', 'nobs_user_account_numbers',
        'loan_products', 'loan_fees', 'account_types', 'loan_product_fees'
    ];

    private $forbiddenColumns = [
        'password', 'remember_token', 'api_token', 'secret', 'key', 'auth', 'iv', 'salt'
    ];

    public function __construct() {}

    public function chat(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $compId = $user->comp_id;
            $this->intentLibrary = new AiIntentLibrary($compId);
            $this->actionManager = new AiActionManager();

            $prompt = $request->input('message');
            $sessionId = $request->input('session_id');
            $model = $request->input('model', 'gemini-3-flash-preview');
            $requestApiKey = $request->input('api_key');
            $userContext = $request->input('user_context') ?: [];

            if (empty($prompt)) return response()->json(['success' => false, 'message' => 'Message is required'], 400);

            $apiKey = $requestApiKey ?: env('GOOGLE_AI_API_KEY');
            if (!$apiKey) return response()->json(['success' => false, 'message' => 'AI API Key is missing.'], 400);

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
        
        $maxTurns = 4; $currentTurn = 0; $lastResponse = null; $lastToolOutput = null; $activeUiType = 'text';

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

            $history[] = $candidate; $history[] = ['role' => 'function', 'parts' => $toolResults];
            $currentTurn++;
        }

        return ['response' => $lastResponse, 'ui_type' => $activeUiType, 'ui_metadata' => $lastToolOutput];
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
                    
                    if ($intent === 'TOTAL_DEPOSITS') $output = $this->intentLibrary->getFinancialSummary('Deposit', $params['date'] ?? null);
                    elseif ($intent === 'TOTAL_WITHDRAWALS') $output = $this->intentLibrary->getFinancialSummary('Withdraw', $params['date'] ?? null);
                    elseif ($intent === 'CUSTOMER_SEARCH') $output = $this->intentLibrary->searchCustomers($params['term'] ?? '');
                    elseif ($intent === 'LOAN_OVERVIEW') $output = $this->intentLibrary->getLoanOverview();
                    elseif ($intent === 'ACCOUNT_SUMMARY') $output = $this->intentLibrary->getAccountSummary($params['date'] ?? null);
                    elseif ($intent === 'HELP_MENU') $output = $this->intentLibrary->getHelpMenu($userContext['user']['type'] ?? 'Staff');
                } 
                elseif ($name === 'execute_analytical_query') {
                    $output = $this->executeSecureSql($args['sql']);
                }

                if ($output) {
                    $uiType = $output['ui_type']; $rawData = $output['ui_metadata'];
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
        if (preg_match('/(\*|DROP|DELETE|UPDATE|INSERT|TRUNCATE|ALTER|CREATE|REPLACE|UNION|GRANT|REVOKE|EXEC)/i', $sql)) {
            throw new \Exception("Security Violation: Unauthorized query pattern detected.");
        }
        
        foreach ($this->forbiddenColumns as $col) {
            if (preg_match("/\b$col\b/i", $sql)) throw new \Exception("Security Violation: Access to system column '$col' is forbidden.");
        }

        $targetTable = null;
        foreach ($this->allowedTables as $table) {
            if (preg_match("/\b$table\b/i", $sql)) { $targetTable = $table; break; }
        }
        if (!$targetTable) throw new \Exception("Security Violation: Unauthorized table access.");

        $compId = (int)auth('api')->user()->comp_id;
        
        // Multi-Tenancy Token Binding
        if (strpos($sql, '{COMP_ID}') !== false) {
            $sql = str_replace('{COMP_ID}', $compId, $sql);
        } else {
            if (stripos($sql, 'WHERE') !== false) {
                $sql = preg_replace('/WHERE/i', "WHERE comp_id = $compId AND ", $sql, 1);
            } else {
                $injection = " WHERE comp_id = $compId ";
                if (preg_match('/(GROUP BY|ORDER BY|LIMIT)/i', $sql, $matches, PREG_OFFSET_CAPTURE)) {
                    $pos = $matches[0][1];
                    $sql = substr($sql, 0, $pos) . $injection . substr($sql, $pos);
                } else { $sql .= $injection; }
            }
        }

        try {
            Log::info("AI Secure SQL: $sql");
            $results = DB::select($sql);
            return ['ui_type' => 'data_table', 'ui_metadata' => $results, 'caption' => "I've analyzed the data and found " . count($results) . " records."];
        } catch (QueryException $qe) {
            throw new \Exception("Query plan error. Please refine your question.");
        }
    }

    private function getAvailableTools()
    {
        return [[
            'function_declarations' => [
                [
                    'name' => 'fetch_from_library',
                    'description' => 'Fetches pre-verified reports and help menus using standard bank logic.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intent_name' => ['type' => 'string', 'enum' => ['TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'ACCOUNT_SUMMARY', 'CUSTOMER_SEARCH', 'LOAN_OVERVIEW', 'HELP_MENU']],
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
                            'sql' => ['type' => 'string', 'description' => 'SQL SELECT statement. Use {COMP_ID} for tenant isolation. Qualify joins.']
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
        $validModels = ['gemini-2.5-pro', 'gemini-2.5-flash', 'gemini-3-flash-preview', 'gemini-1.5-pro'];
        if (!in_array($model, $validModels)) $model = 'gemini-1.5-flash'; 

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $greeting = "You are the SABS Bank AI Assistant.";
        if ($userContext && isset($userContext['user'])) {
            $u = $userContext['user']; $c = $userContext['company'];
            $greeting .= " Speaking to " . ($u['title'] ?? '') . " " . ($u['name'] ?? 'User') . " (Role: " . ($u['type'] ?? 'Staff') . ") at " . ($c['name'] ?? 'SABS Bank') . ".";
        }

        $systemInstruction = "$greeting 
        Context: Company ID $compId. Server Date " . date('Y-m-d') . ".
        MISSION: Senior Financial Analyst. 100% data grounding. 0% hallucination.
        
        PRODUCTION CALCULATIONS (MUST FOLLOW):
        1. DEPOSITS: Sum `amount` from `nobs_transactions` where `name_of_transaction` = 'Deposit'. ALWAYS exclude `amount` >= 1,000,000. ALWAYS exclude records with 'reversal' in name or description.
        2. LOAN PAID AMOUNT: `SUM(principal_paid + interest_paid + fees_paid)` from `loan_repayment_schedules`.
        3. LOAN OUTSTANDING: `loan_applications.total_repayment - (LOAN PAID AMOUNT)`.
        4. ARREARS: `loan_repayment_schedules` where `due_date < CURRENT_DATE` AND `status != 'paid'`.
        
        FEW-SHOT SQL:
        - Q: 'Who is in arrears?' -> SQL: `SELECT r.first_name, r.surname, (s.principal_due + s.interest_due + s.fees_due - (s.principal_paid + s.interest_paid + s.fees_paid)) as amount_due FROM loan_repayment_schedules s JOIN loan_applications a ON s.loan_application_id = a.id JOIN nobs_registration r ON a.customer_id = r.id WHERE s.comp_id = {COMP_ID} AND s.due_date < CURRENT_DATE AND s.status != 'paid'`
        
        RULES:
        1. FIRST MESSAGE: Welcome + call `fetch_from_library(intent_name='HELP_MENU')`.
        2. SQL isolation: MUST use `comp_id = {COMP_ID}`. Qualify columns.
        3. INTEGRITY: Never invent data. Use the production formulas above.";

        $payload = ['system_instruction' => ['parts' => [['text' => $systemInstruction]]], 'contents' => $history, 'tools' => $tools, 'generationConfig' => ['temperature' => 0.1, 'topP' => 0.95]];
        $response = Http::post($url, $payload);
        if ($response->failed()) throw new \Exception("AI connection error.");
        return $response->json();
    }
}
