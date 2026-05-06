<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AiIntentLibrary;
use App\Services\AiActionManager;
use App\Services\AiIntelligenceService;

class AiAgentController extends Controller
{
    private $intentLibrary;
    private $actionManager;
    private $intelligenceService;

    public function __construct() {}

    private function initServices($compId, $apiKey = null, $model = null)
    {
        $this->intentLibrary = new AiIntentLibrary($compId);
        $this->actionManager = new AiActionManager();
        $this->intelligenceService = new AiIntelligenceService($compId, $apiKey, $model);
    }

    public function chat(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $compId = $user->comp_id;
            
            // Check if AI Chat is enabled for this company (Defensive Guard)
            try {
                $company = DB::table('accounts')->where('id', $compId)->first();
                $chatEnabled = optional($company)->ai_chat_enabled ?? 1;
            } catch (\Throwable $e) {
                $chatEnabled = 1; // Default to enabled if column missing
            }

            if (!$chatEnabled) {
                return response()->json(['success' => false, 'message' => 'AI Assistant is disabled by administrator.'], 403);
            }

            $requestApiKey = $request->input('api_key');
            $apiKey = $requestApiKey ?: env('GOOGLE_AI_API_KEY');
            $model = $request->input('model', 'gemini-3-flash-preview');
            
            $this->initServices($compId, $apiKey, $model);
            
            $prompt = $request->input('message');
            $sessionId = $request->input('session_id');
            $userContext = $request->input('user_context') ?: [];

            if (empty($prompt)) return response()->json(['success' => false, 'message' => 'Message is required'], 400);

            if (!$apiKey) return response()->json(['success' => false, 'message' => 'AI API Key is missing.'], 400);

            $session = $this->getOrCreateSession($user, $sessionId, $model);
            $this->storeMessage($session->id, 'user', $prompt);

            // Process with Gemini using the "Verified Toolset"
            $result = $this->processWithGemini($session, $prompt, $model, $apiKey, $compId, $userContext);

            // ENFORCE CONVERSATIONAL TEXT
            $finalText = $result['response']['candidates'][0]['content']['parts'][0]['text'] ?? '';
            // If model response is empty or generic, use the tool's caption
            if (empty(trim($finalText)) || strlen($finalText) < 10) {
                if (isset($result['caption'])) {
                     $finalText = $result['caption'];
                }
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'response' => $result['response'],
                'final_text' => $finalText,
                'ui_type' => $result['ui_type'],
                'ui_metadata' => $result['ui_metadata']
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => "I encountered an issue: " . $e->getMessage()], 500);
        }
    }

    /**
     * Proactive: Fetches briefing on login/startup
     */
    public function getOnboardingBrief(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $compId = $user->comp_id;
            $company = DB::table('accounts')->where('id', $compId)->first();
            
            // SQL Guard: If migration hasn't run, default to enabled
            try {
                $execEnabled = optional($company)->ai_exec_briefing_enabled ?? 1;
                $coachEnabled = optional($company)->ai_growth_coach_enabled ?? 1;
            } catch (\Throwable $e) {
                $execEnabled = 1; $coachEnabled = 1;
            }

            $apiKey = $request->input('api_key') ?: env('GOOGLE_AI_API_KEY');
            $model = $request->input('model', 'gemini-3-flash-preview');
            $this->initServices($compId, $apiKey, $model);
            
            // Normalized Role Check
            $role = strtolower($user->type_name ?? $user->type ?? 'Staff');
            $isAdmin = in_array($role, ['admin', 'owner', 'super admin', 'manager', 'god admin']);

            if ($isAdmin && $execEnabled) {
                return response()->json(['success' => true, 'data' => $this->intelligenceService->getExecutiveBriefing('monthly')]);
            } 
            
            if (($role === 'agent' || $role === 'staff') && $coachEnabled) {
                return response()->json(['success' => true, 'data' => $this->intelligenceService->getAgentCoaching($user->id)]);
            }

            return response()->json(['success' => false, 'message' => 'No briefing available or disabled.']);
        } catch (\Throwable $e) {
            Log::error("AI Briefing Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Proactive: Fetches risk assessment for a loan
     */
    public function getRiskShield(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $compId = $user->comp_id;
            $company = DB::table('accounts')->where('id', $compId)->first();

            // SQL Guard: If migration hasn't run, default to enabled
            try {
                $riskEnabled = optional($company)->ai_risk_shield_enabled ?? 1;
            } catch (\Throwable $e) {
                $riskEnabled = 1;
            }

            if (!$riskEnabled) {
                return response()->json(['success' => false, 'message' => 'Risk shield is disabled.'], 403);
            }

            $customerId = $request->input('customer_id');
            $loanAmount = $request->input('amount');
            $apiKey = $request->input('api_key') ?: env('GOOGLE_AI_API_KEY');
            $model = $request->input('model', 'gemini-3-flash-preview');
            
            if (!$customerId) return response()->json(['success' => false, 'message' => 'Customer ID required'], 400);

            $this->initServices($compId, $apiKey, $model);
            $analysis = $this->intelligenceService->getRiskAnalysis($customerId, $loanAmount);

            return response()->json(['success' => true, 'data' => $analysis]);
        } catch (\Throwable $e) {
            Log::error("AI Risk Shield Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAiSettings()
    {
        try {
            $user = auth('api')->user();
            $settings = DB::table('accounts')->where('id', $user->comp_id)->first();
            
            // SQL Guard: If migration hasn't run, default to enabled for all
            return response()->json([
                'success' => true, 
                'settings' => [
                    'ai_chat_enabled' => optional($settings)->ai_chat_enabled ?? 1,
                    'ai_risk_shield_enabled' => optional($settings)->ai_risk_shield_enabled ?? 1,
                    'ai_growth_coach_enabled' => optional($settings)->ai_growth_coach_enabled ?? 1,
                    'ai_exec_briefing_enabled' => optional($settings)->ai_exec_briefing_enabled ?? 1,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true, 
                'settings' => [
                    'ai_chat_enabled' => 1, 'ai_risk_shield_enabled' => 1, 
                    'ai_growth_coach_enabled' => 1, 'ai_exec_briefing_enabled' => 1
                ]
            ]);
        }
    }

    public function updateAiSettings(Request $request)
    {
        $user = auth('api')->user();
        // Simple permission check: only managers/admins
        if (!in_array(strtolower($user->type_name), ['admin', 'owner', 'manager'])) {
            return response()->json(['success' => false, 'message' => 'Only administrators can change AI settings.'], 403);
        }

        DB::table('accounts')->where('id', $user->comp_id)->update([
            'ai_chat_enabled' => $request->input('ai_chat_enabled', 1),
            'ai_risk_shield_enabled' => $request->input('ai_risk_shield_enabled', 1),
            'ai_growth_coach_enabled' => $request->input('ai_growth_coach_enabled', 1),
            'ai_exec_briefing_enabled' => $request->input('ai_exec_briefing_enabled', 1),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'AI Configuration saved.']);
    }

    public function clearChat(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

            $sessionId = $request->input('session_id');
            if (!$sessionId) return response()->json(['success' => false, 'message' => 'Session ID required'], 400);

            $session = DB::table('ai_chat_sessions')->where('id', $sessionId)->where('user_id', $user->id)->first();
            if (!$session) return response()->json(['success' => false, 'message' => 'Session not found'], 404);

            DB::table('ai_messages')->where('session_id', $sessionId)->delete();
            DB::table('ai_chat_sessions')->where('id', $sessionId)->delete();

            return response()->json(['success' => true, 'message' => 'Chat history cleared.']);
        } catch (\Throwable $e) {
            Log::error("AI Clear Chat Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not clear chat.'], 500);
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
        $this->pruneOldMessages(); // Run GC on new session

        $id = DB::table('ai_chat_sessions')->insertGetId([
            'user_id' => $user->id,
            'comp_id' => $user->comp_id,
            'model_name' => $model,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return DB::table('ai_chat_sessions')->where('id', $id)->first();
    }

    private function pruneOldMessages()
    {
        try {
            DB::table('ai_messages')->where('created_at', '<', now()->subDays(7))->delete();
            DB::table('ai_chat_sessions')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('ai_messages')
                          ->whereRaw('ai_messages.session_id = ai_chat_sessions.id');
                })
                ->where('created_at', '<', now()->subDays(1))
                ->delete();
        } catch (\Throwable $e) {
            Log::error("AI GC Error: " . $e->getMessage());
        }
    }

    private function storeMessage($sessionId, $role, $content, $toolCalls = null, $uiType = 'text', $uiMetadata = null, $toolCallId = null)
    {
        $meta = $uiMetadata !== null ? (is_string($uiMetadata) ? $uiMetadata : json_encode($uiMetadata)) : null;
        DB::table('ai_chat_sessions')->where('id', $sessionId)->update(['updated_at' => now()]);

        return DB::table('ai_messages')->insertGetId([
            'session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'tool_calls' => $toolCalls ? (is_string($toolCalls) ? $toolCalls : json_encode($toolCalls)) : null,
            'tool_call_id' => $toolCallId,
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
        
        $maxTurns = 4; $currentTurn = 0; $lastResponse = null; $lastToolOutput = null; $activeUiType = 'text'; $lastCaption = null;

        while ($currentTurn < $maxTurns) {
            $response = $this->callGeminiApi($model, $apiKey, $history, $tools, $compId, $userContext);
            $lastResponse = $response;
            $candidate = $response['candidates'][0]['content'] ?? null;

            if (!$candidate) break;

            $modelText = null;
            $toolCalls = [];
            foreach ($candidate['parts'] as $part) {
                if (isset($part['functionCall'])) {
                    $toolCalls[] = $part;
                } elseif (isset($part['text'])) {
                    $modelText = $part['text'];
                }
            }

            if (empty($toolCalls)) {
                if ($modelText !== null && $modelText !== '') {
                    $this->storeMessage($session->id, 'model', $modelText, null, $activeUiType, $lastToolOutput);
                }
                break;
            }

            $this->storeMessage($session->id, 'model', $modelText, $toolCalls, $activeUiType, null);

            $execution = $this->handleToolCalls($session, $toolCalls, $userContext);
            $toolResults = $execution['results'];
            
            if ($execution['raw_data'] !== null) $lastToolOutput = $execution['raw_data'];
            if ($execution['ui_type'] !== 'text') $activeUiType = $execution['ui_type'];
            if (isset($execution['caption'])) $lastCaption = $execution['caption'];
            
            foreach ($toolResults as $tr) {
                $this->storeMessage(
                    $session->id, 
                    'tool', 
                    json_encode($tr['functionResponse']['response']), 
                    null, 
                    $activeUiType, 
                    null, 
                    $tr['functionResponse']['name']
                );
            }

            $leanToolResults = array_map(function($tr) {
                $copy = $tr;
                if (isset($copy['functionResponse']['response']['data'])) {
                    unset($copy['functionResponse']['response']['data']);
                }
                return $copy;
            }, $toolResults);

            $history[] = $candidate; 
            $history[] = ['role' => 'function', 'parts' => $leanToolResults];
            $currentTurn++;
        }

        return [
            'response' => $lastResponse, 
            'ui_type' => $activeUiType, 
            'ui_metadata' => $lastToolOutput,
            'caption' => $lastCaption
        ];
    }

    private function handleToolCalls($session, $toolCalls, $userContext = null)
    {
        $results = []; $rawData = null; $uiType = 'text'; $caption = null;
        $user = auth('api')->user();
        $role = strtolower($user->type_name ?? $user->type ?? 'Staff');
        $isAdmin = in_array($role, ['admin', 'manager', 'owner', 'super admin', 'god admin']);

        foreach ($toolCalls as $call) {
            $func = $call['functionCall'];
            $args = $func['args'] ?? [];
            $name = $func['name'];
            $output = null;

            try {
                if ($name === 'fetch_from_library') {
                    $intent = $args['intent_name'];
                    $params = $args['params'] ?? [];
                    $startDate = $params['start_date'] ?? ($args['start_date'] ?? ($params['date'] ?? ($args['date'] ?? null)));
                    $endDate = $params['end_date'] ?? ($args['end_date'] ?? null);
                    $month = $params['month'] ?? ($args['month'] ?? null);
                    $menu = $params['menu'] ?? ($args['menu'] ?? 'main');
                    $term = $params['term'] ?? ($args['term'] ?? '');
                    $period = $params['period'] ?? ($args['period'] ?? 'monthly');
                    
                    if ($intent === 'TOTAL_DEPOSITS') $output = $this->intentLibrary->getFinancialSummary('Deposit', $startDate, $endDate);
                    elseif ($intent === 'TOTAL_WITHDRAWALS') $output = $this->intentLibrary->getFinancialSummary('Withdraw', $startDate, $endDate);
                    elseif ($intent === 'CUSTOMER_SEARCH') $output = $this->intentLibrary->searchCustomers($term);
                    elseif ($intent === 'ARREARS_REPORT') $output = $this->intentLibrary->getArrearsList();
                    elseif ($intent === 'BANK_LIQUIDITY') $output = $this->intentLibrary->getSystemLiquidity();
                    elseif ($intent === 'AGENT_RANKING') $output = $this->intentLibrary->getAgentPerformance($month);
                    elseif ($intent === 'PORTFOLIO_HEALTH') $output = $this->intentLibrary->getPortfolioSummary();
                    elseif ($intent === 'HELP_MENU') $output = $this->intentLibrary->getHelpMenu($role, $menu);
                    elseif ($intent === 'ACCOUNT_BALANCES') $output = $this->intentLibrary->getAccountBalancesByType();
                    elseif ($intent === 'CASH_POOL_BALANCE') $output = $this->intentLibrary->getCashAndPool();
                    elseif ($intent === 'DAILY_SUMMARY') $output = $this->intentLibrary->getDailySummary($startDate, $endDate);
                    elseif ($intent === 'RECENT_TRANSACTIONS') $output = $this->intentLibrary->getRecentTransactions(5, $startDate, $endDate);
                    elseif ($intent === 'NEW_REGISTRATIONS') $output = $this->intentLibrary->getDailySummary($startDate, $endDate);
                    elseif ($intent === 'RECENT_CUSTOMERS') $output = $this->intentLibrary->getRecentRegistrations(10, $startDate, $endDate);
                    elseif ($intent === 'EXPECTED_REPAYMENTS') $output = $this->intentLibrary->getExpectedRepayments($startDate, $endDate);
                    elseif ($intent === 'DAILY_DISBURSEMENTS') $output = $this->intentLibrary->getDailyDisbursements($startDate, $endDate);
                    elseif ($intent === 'PENDING_LOANS') $output = $this->intentLibrary->getPendingLoans();
                    elseif ($intent === 'AGENT_COLLECTIONS') $output = $this->intentLibrary->getAgentCollections($startDate, $endDate);
                    elseif ($intent === 'EXECUTIVE_BRIEFING') $output = $this->intelligenceService->getExecutiveBriefing($period);
                    elseif ($intent === 'DORMANT_ACCOUNTS') {
                        if ($isAdmin) {
                            $output = $this->intentLibrary->getDormantAccounts();
                        } else {
                            $output = ['ui_type' => 'text', 'ui_metadata' => [], 'caption' => 'Unauthorized: Management only.'];
                        }
                    }
                } 

                if ($output) {
                    $uiType = $output['ui_type']; 
                    $rawData = $output['ui_metadata'];
                    $caption = $output['caption'] ?? null;
                    $results[] = ['functionResponse' => ['name' => $name, 'response' => ['result' => $output['caption'] ?? 'Task completed.', 'data' => $output['ui_metadata']]]];
                }
            } catch (\Throwable $e) {
                $results[] = ['functionResponse' => ['name' => $name, 'response' => ['error' => $e->getMessage()]]];
            }
        }
        return ['results' => $results, 'raw_data' => $rawData, 'ui_type' => $uiType, 'caption' => $caption];
    }

    private function getAvailableTools()
    {
        return [[
            'function_declarations' => [
                [
                    'name' => 'fetch_from_library',
                    'description' => 'Executes verified bank business logic for reports, search, and health metrics.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intent_name' => [
                                'type' => 'string', 
                                'enum' => [
                                    'TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'BANK_LIQUIDITY', 'TREASURY_POSITION',
                                    'AGENT_LIABILITY', 'EXPENSE_REPORT', 'ARREARS_REPORT', 
                                    'AGENT_RANKING', 'PORTFOLIO_HEALTH', 'CUSTOMER_SEARCH', 'HELP_MENU',
                                    'ACCOUNT_BALANCES', 'CASH_POOL_BALANCE', 'DAILY_SUMMARY', 'RECENT_TRANSACTIONS',
                                    'NEW_REGISTRATIONS', 'RECENT_CUSTOMERS', 'EXPECTED_REPAYMENTS', 'DAILY_DISBURSEMENTS',
                                    'PENDING_LOANS', 'AGENT_COLLECTIONS', 'EXECUTIVE_BRIEFING', 'DORMANT_ACCOUNTS'
                                ]
                            ],
                            'params' => [
                                'type' => 'object',
                                'properties' => [
                                    'term' => ['type' => 'string', 'description' => 'Name/Account for search'],
                                    'date' => ['type' => 'string', 'description' => 'YYYY-MM-DD (Exact date)'],
                                    'start_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD (Start of range)'],
                                    'end_date' => ['type' => 'string', 'description' => 'YYYY-MM-DD (End of range)'],
                                    'month' => ['type' => 'string', 'description' => 'MM (01-12)'],
                                    'menu' => ['type' => 'string', 'description' => 'liquidity|transactions|customers|loans|performance|main'],
                                    'period' => ['type' => 'string', 'description' => 'daily|weekly|monthly|yearly|alltime']
                                ]
                            ]
                        ],
                        'required' => ['intent_name']
                    ]
                ]
            ]
        ]];
    }

    private function getHistory($sessionId)
    {
        $messages = DB::table('ai_messages')
            ->where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        $ordered = $messages->reverse();
        $rawMessages = [];
        foreach ($ordered as $msg) {
            $role = ($msg->role === 'tool') ? 'function' : $msg->role;
            $parts = [];

            if ($msg->role === 'user' || $msg->role === 'model') {
                if ($msg->content !== null && $msg->content !== '') {
                    $parts[] = ['text' => $msg->content];
                }
                if ($msg->tool_calls) {
                    $calls = json_decode($msg->tool_calls, true);
                    if (is_array($calls)) {
                        foreach ($calls as $call) $parts[] = $call;
                    }
                }
            } else if ($msg->role === 'tool') {
                $fullResponse = json_decode($msg->content, true);
                $leanResponse = ['result' => $fullResponse['result'] ?? 'Task completed.'];
                $parts[] = ['functionResponse' => ['name' => $msg->tool_call_id ?? 'fetch_from_library', 'response' => $leanResponse]];
            }

            if (empty($parts)) continue;

            $lastIndex = count($rawMessages) - 1;
            if ($lastIndex >= 0 && $rawMessages[$lastIndex]['role'] === $role) {
                $rawMessages[$lastIndex]['parts'] = array_merge($rawMessages[$lastIndex]['parts'], $parts);
            } else {
                $rawMessages[] = ['role' => $role, 'parts' => $parts];
            }
        }

        while (!empty($rawMessages) && $rawMessages[0]['role'] !== 'user') {
            array_shift($rawMessages);
        }

        return $rawMessages;
    }

    private function callGeminiApi($model, $apiKey, $history, $tools, $compId, $userContext = null)
    {
        $validModels = ['gemini-2.5-pro', 'gemini-2.5-flash', 'gemini-3-flash-preview', 'gemini-1.5-pro'];
        if (!in_array($model, $validModels)) $model = 'gemini-1.5-flash'; 

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $greeting = "You are the SABS Bank Executive AI Analyst.";
        if ($userContext && isset($userContext['user'])) {
            $u = $userContext['user']; $c = $userContext['company'];
            $greeting .= " Greeting " . ($u['name'] ?? 'User') . " at " . ($c['name'] ?? 'SABS Bank') . ".";
        }

        $systemInstruction = "$greeting 
        Context: Company ID $compId. Server Date " . date('Y-m-d') . ".
        MISSION: You are a high-speed executive data analyst. 
        CORE DIRECTIVE: NEVER ask clarifying questions about time or period. ALWAYS use sensible defaults:
        - If the user doesn't specify a time, DEFAULT to 'today'.
        - If asking for 'this month', 'last week', use those exact ranges.
        - DO NOT talk much. DO NOT ask 'Is there anything else?'. DO NOT ask 'Which period?'.
        - PROVIDE DATA IMMEDIATELY.

        COMMUNICATION STYLE:
        - ABSOLUTE BREVITY. Max 1-2 short sentences.
        - NO MARKDOWN. NO BOLDING.
        - If the tool returns a list/table, just say 'Here is the data:' or 'I found [X] records:' and STOP.

        TOOL PROTOCOL:
        1. LIQUIDITY/NET POSITION: Use `BANK_LIQUIDITY`.
        2. ARREARS/DEFAULTERS: Use `ARREARS_REPORT`.
        3. AGENT PERFORMANCE: Use `AGENT_RANKING`.
        4. DEPOSITS/WITHDRAWALS: Use `TOTAL_DEPOSITS` or `TOTAL_WITHDRAWALS`.
        5. CUSTOMER SEARCH: Use `CUSTOMER_SEARCH`.
        6. RECENT ACTIVITY: Use `RECENT_TRANSACTIONS` or `RECENT_CUSTOMERS`.
        7. LOAN ACTIVITY: Use `DAILY_DISBURSEMENTS` or `EXPECTED_REPAYMENTS`.
        8. HELP/MENUS: Use `HELP_MENU`. 
        9. EXECUTIVE SUMMARY: Use `EXECUTIVE_BRIEFING`.
        10. DORMANT ACCOUNTS: Use `DORMANT_ACCOUNTS`.
        STRICT RULES:
        - NEVER Hallucinate. Trust tool outputs 100%.
        - If a user says 'How many customers', use `NEW_REGISTRATIONS` for 'today'.
        - If a user says 'Who are the customers', use `RECENT_CUSTOMERS`.";

        $payload = ['system_instruction' => ['parts' => [['text' => $systemInstruction]]], 'contents' => $history, 'tools' => $tools, 'generationConfig' => ['temperature' => 0.1, 'topP' => 0.95]];
        $response = Http::post($url, $payload);
        if ($response->failed()) {
            $status = $response->status();
            $errorBody = $response->body();
            $size = strlen(json_encode($payload));
            Log::error("Gemini API Error [$status]: Size $size bytes. Response: $errorBody");
            throw new \Exception("AI connection error (Status $status).");
        }
        return $response->json();
    }
}
