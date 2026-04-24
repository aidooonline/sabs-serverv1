<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AiIntentLibrary;
use App\Services\AiActionManager;

class AiAgentController extends Controller
{
    private $intentLibrary;
    private $actionManager;

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

            // Process with Gemini using the "Verified Toolset"
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
                    elseif ($intent === 'ARREARS_REPORT') $output = $this->intentLibrary->getArrearsList();
                    elseif ($intent === 'BANK_LIQUIDITY') $output = $this->intentLibrary->getSystemLiquidity();
                    elseif ($intent === 'AGENT_RANKING') $output = $this->intentLibrary->getAgentPerformance($params['month'] ?? null);
                    elseif ($intent === 'PORTFOLIO_HEALTH') $output = $this->intentLibrary->getPortfolioSummary();
                    elseif ($intent === 'HELP_MENU') $output = $this->intentLibrary->getHelpMenu($userContext['user']['type'] ?? 'Staff');
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
                                'enum' => ['TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'BANK_LIQUIDITY', 'ARREARS_REPORT', 'AGENT_RANKING', 'PORTFOLIO_HEALTH', 'CUSTOMER_SEARCH', 'HELP_MENU']
                            ],
                            'params' => [
                                'type' => 'object',
                                'properties' => [
                                    'term' => ['type' => 'string', 'description' => 'Name/Account for search'],
                                    'date' => ['type' => 'string', 'description' => 'YYYY-MM-DD'],
                                    'month' => ['type' => 'string', 'description' => 'MM (01-12)']
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

        $greeting = "You are the SABS Bank Executive AI Analyst.";
        if ($userContext && isset($userContext['user'])) {
            $u = $userContext['user']; $c = $userContext['company'];
            $greeting .= " Greeting " . ($u['name'] ?? 'User') . " at " . ($c['name'] ?? 'SABS Bank') . ".";
        }

        $systemInstruction = "$greeting 
        Context: Company ID $compId. Server Date " . date('Y-m-d') . ".
        
        MISSION: You are a secure analytical assistant. YOU MUST ONLY use the provided tools to fetch financial data. 
        NEVER attempt to generate your own SQL or guess financial numbers.
        
        TOOL PROTOCOL:
        1. LIQUIDITY/NET POSITION: Use `BANK_LIQUIDITY`.
        2. ARREARS/DEFAULTERS: Use `ARREARS_REPORT`.
        3. AGENT PERFORMANCE: Use `AGENT_RANKING`.
        4. DEPOSITS/WITHDRAWALS: Use `TOTAL_DEPOSITS` or `TOTAL_WITHDRAWALS`.
        5. CUSTOMER SEARCH: Use `CUSTOMER_SEARCH`.
        6. START OF SESSION: If history is empty, say welcome and call `HELP_MENU`.
        
        STRICT RULES:
        - If the user asks a question not covered by the library tools, politely say you only provide verified bank reports.
        - NEVER Hallucinate. Trust the tool outputs 100%.";

        $payload = ['system_instruction' => ['parts' => [['text' => $systemInstruction]]], 'contents' => $history, 'tools' => $tools, 'generationConfig' => ['temperature' => 0.1, 'topP' => 0.95]];
        $response = Http::post($url, $payload);
        if ($response->failed()) throw new \Exception("AI connection error.");
        return $response->json();
    }
}
