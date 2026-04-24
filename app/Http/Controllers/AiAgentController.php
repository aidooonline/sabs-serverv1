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
        'capital_account_transactions', 'nobs_user_account_numbers'
    ];

    private $forbiddenColumns = [
        'password', 'remember_token', 'api_token', 'secret', 'key', 'auth'
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
            Log::error("AI Chat Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => "I encountered an issue: " . $e->getMessage()], 500);
        }
    }

    public function executeAction(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            
            $payload = $request->input('payload');
            if (!$payload) return response()->json(['success' => false, 'message' => 'Invalid action payload.'], 400);

            $this->actionManager = new AiActionManager();
            $result = $this->actionManager->executeAction($payload, $user);
            
            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error("AI Execute Action Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Execution failed.'], 500);
        }
    }

    private function getOrCreateSession($user, $sessionId, $model)
    {
        if ($sessionId) {
            $sess = DB::table('ai_chat_sessions')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();
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
        $meta = null;
        if ($uiMetadata !== null) {
            $meta = is_string($uiMetadata) ? $uiMetadata : json_encode($uiMetadata);
        }

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
        
        $maxTurns = 3;
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
                if (isset($part['functionCall'])) {
                    $toolCalls[] = $part;
                }
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
                DB::table('ai_messages')
                    ->where('session_id', $session->id)
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->update(['tool_call_id' => $tr['functionResponse']['name']]);
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
        $results = [];
        $rawData = null;
        $uiType = 'text';

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
                    elseif ($intent === 'HELP_MENU') {
                        $role = $userContext['user']['type'] ?? 'Staff';
                        $output = $this->intentLibrary->getHelpMenu($role);
                    }
                    elseif ($intent === 'ACCOUNT_SUMMARY') $output = $this->intentLibrary->getAccountSummary($params['date'] ?? null);
                } 
                elseif ($name === 'prepare_bank_action') {
                    $action = $args['action_type'];
                    $output = $this->actionManager->prepareAction($action, $args['params'] ?? []);
                }
                elseif ($name === 'execute_analytical_query') {
                    $sql = $args['sql'];
                    $output = $this->executeSecureSql($sql);
                }

                if ($output) {
                    $uiType = $output['ui_type'];
                    $rawData = $output['ui_metadata'];
                    $results[] = [
                        'functionResponse' => [
                            'name' => $name,
                            'response' => ['result' => $output['caption'], 'data' => $output['ui_metadata']]
                        ]
                    ];
                }
            } catch (\Throwable $e) {
                Log::error("AI Tool Routing Error: " . $e->getMessage());
                $results[] = [
                    'functionResponse' => [
                        'name' => $name, 
                        'response' => ['error' => 'Tool failed: ' . $e->getMessage()]
                    ]
                ];
            }
        }
        return ['results' => $results, 'raw_data' => $rawData, 'ui_type' => $uiType];
    }

    /**
     * Executes AI-generated SQL with rigorous multi-layered security.
     */
    private function executeSecureSql($sql)
    {
        // 1. Strict Read-Only Guard
        if (preg_match('/(DROP|DELETE|UPDATE|INSERT|TRUNCATE|ALTER|CREATE|REPLACE|GRANT|REVOKE|EXEC)/i', $sql)) {
            throw new \Exception("Security Violation: Only SELECT queries are permitted.");
        }

        // 2. Column Blacklist Check (Prevent PII leaks)
        foreach ($this->forbiddenColumns as $col) {
            if (stripos($sql, $col) !== false) {
                throw new \Exception("Security Violation: Access to system column '$col' is forbidden.");
            }
        }

        // 3. Table Whitelist Check
        $foundAllowedTable = false;
        foreach ($this->allowedTables as $table) {
            if (stripos($sql, $table) !== false) {
                $foundAllowedTable = true;
                break;
            }
        }
        if (!$foundAllowedTable) {
            throw new \Exception("Security Violation: Unauthorized table access attempted.");
        }

        // 4. Automated Multi-Tenancy Injection (Robust)
        $user = auth('api')->user();
        $compId = (int)$user->comp_id;

        // We use a more surgical injection that only targets the first WHERE or adds it safely
        if (stripos($sql, 'WHERE') !== false) {
            // Replace ONLY the first 'WHERE' to avoid breaking subqueries
            $sql = preg_replace('/WHERE/i', "WHERE comp_id = $compId AND ", $sql, 1);
        } else {
            // Check for GROUP BY, ORDER BY, or LIMIT to inject before
            if (preg_match('/(GROUP BY|ORDER BY|LIMIT)/i', $sql, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1];
                $sql = substr($sql, 0, $pos) . " WHERE comp_id = $compId " . substr($sql, $pos);
            } else {
                $sql .= " WHERE comp_id = $compId";
            }
        }

        try {
            Log::info("AI Secure SQL executing: $sql");
            $results = DB::select($sql);
            
            return [
                'ui_type' => 'data_table',
                'ui_metadata' => $results,
                'caption' => "Analysis complete. I found " . count($results) . " matching records."
            ];
        } catch (QueryException $qe) {
            Log::error("AI SQL Syntax Error: " . $qe->getMessage());
            throw new \Exception("Database syntax error. Please refine your query logic.");
        }
    }

    private function getAvailableTools()
    {
        return [[
            'function_declarations' => [
                [
                    'name' => 'fetch_from_library',
                    'description' => 'Fetches pre-verified reports and data from the bank library.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intent_name' => [
                                'type' => 'string', 
                                'enum' => ['TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'ACCOUNT_SUMMARY', 'CUSTOMER_SEARCH', 'LOAN_OVERVIEW', 'RECENT_ACTIVITY', 'HELP_MENU']
                            ],
                            'params' => [
                                'type' => 'object', 
                                'properties' => [
                                    'term' => ['type' => 'string', 'description' => 'Search term for customers'],
                                    'date' => ['type' => 'string', 'description' => 'Target date in YYYY-MM-DD format'],
                                    'is_total' => ['type' => 'boolean', 'description' => 'Set to true if user wants TOTAL across ALL account types']
                                ]
                            ]
                        ],
                        'required' => ['intent_name']
                    ]
                ],
                [
                    'name' => 'execute_analytical_query',
                    'description' => 'Executes a raw SQL SELECT query for complex analysis.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'sql' => ['type' => 'string', 'description' => 'SQL SELECT statement. Do NOT include comp_id.']
                        ],
                        'required' => ['sql']
                    ]
                ],
                [
                    'name' => 'prepare_bank_action',
                    'description' => 'Prepares a sensitive write action.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'action_type' => ['type' => 'string', 'enum' => ['reactivate_account', 'toggle_user_status']],
                            'params' => ['type' => 'object']
                        ],
                        'required' => ['action_type']
                    ]
                ]
            ]
        ]];
    }

    private function getHistory($sessionId)
    {
        $messages = DB::table('ai_messages')
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get();

        $history = [];
        foreach ($messages as $msg) {
            if ($msg->role === 'user' || $msg->role === 'model') {
                $parts = [];
                if ($msg->content) $parts[] = ['text' => $msg->content];
                if ($msg->tool_calls) {
                    $tc = json_decode($msg->tool_calls, true);
                    foreach ($tc as $call) $parts[] = $call;
                }
                $history[] = ['role' => $msg->role, 'parts' => $parts];
            } else if ($msg->role === 'tool') {
                $history[] = [
                    'role' => 'function',
                    'parts' => [[
                        'functionResponse' => [
                            'name' => $msg->tool_call_id,
                            'response' => json_decode($msg->content, true)
                        ]
                    ]]
                ];
            }
        }
        return $history;
    }

    private function callGeminiApi($model, $apiKey, $history, $tools, $compId, $userContext = null)
    {
        $validModels = ['gemini-2.5-pro', 'gemini-2.5-flash', 'gemini-3-flash-preview', 'gemini-1.5-flash', 'gemini-1.5-pro'];
        if (!in_array($model, $validModels)) $model = 'gemini-1.5-flash'; 

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $greetingContext = "You are the SABS Bank AI Assistant.";
        if ($userContext && isset($userContext['user'])) {
            $u = $userContext['user'];
            $c = $userContext['company'];
            $greetingContext .= " Speaking to " . ($u['title'] ?? '') . " " . ($u['name'] ?? 'User') . " (Role: " . ($u['type'] ?? 'Staff') . ") at " . ($c['name'] ?? 'SABS Bank') . ".";
        }

        $systemInstruction = "$greetingContext 
        Context: Company ID $compId. Date " . date('Y-m-d') . ".
        
        MISSION: Senior Financial Analyst. 0% hallucination. 100% data grounding.
        
        SCHEMA:
        - `nobs_transactions`: amount, name_of_transaction (Deposit, Withdraw, Loan Repayment), account_number, agentname.
        - `nobs_registration`: first_name, surname, account_number, phone_number.
        - `loan_applications`: status, amount, customer_id.
        - `loan_repayment_schedules`: due_date, total_due, total_paid, status.
        
        RULES:
        1. FIRST MESSAGE: Warm welcome + call `fetch_from_library(intent_name='HELP_MENU')`.
        2. SQL SECURITY: Never add `comp_id`. Select specific columns only.
        3. ERROR HANDLING: If a tool returns an error, explain it simply. NEVER invent data.";

        $payload = [
            'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
            'contents' => $history,
            'tools' => $tools,
            'generationConfig' => ['temperature' => 0.1, 'topP' => 0.95]
        ];

        $response = Http::post($url, $payload);
        if ($response->failed()) throw new \Exception("Gemini API error: " . ($response->json()['error']['message'] ?? 'Unknown'));
        
        return $response->json();
    }
}
