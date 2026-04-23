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

    public function __construct()
    {
        // Don't initialize Auth-dependent services here
    }

    /**
     * Entry point for all AI Chat interactions.
     */
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

            if (empty($prompt)) {
                return response()->json(['success' => false, 'message' => 'Message is required'], 400);
            }

            $apiKey = $requestApiKey ?: env('GOOGLE_AI_API_KEY');
            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'AI API Key is missing.'], 400);
            }

            $session = $this->getOrCreateSession($user, $sessionId, $model);
            $this->storeMessage($session->id, 'user', $prompt);

            // Process with Gemini and get enriched result
            $result = $this->processWithGemini($session, $prompt, $model, $apiKey, $compId);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'response' => $result['response'],
                'ui_type' => $result['ui_type'],
                'ui_metadata' => $result['ui_metadata']
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            $debugMessage = "I encountered a technical issue. Please try again.";
            if (config('app.debug')) {
                $debugMessage = "Debug Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
            }

            return response()->json([
                'success' => false,
                'message' => $debugMessage,
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Secure endpoint to execute a previously prepared AI action.
     */
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
            return DB::table('ai_chat_sessions')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first() ?: $this->createNewSession($user, $model);
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

    private function processWithGemini($session, $prompt, $model, $apiKey, $compId)
    {
        $history = $this->getHistory($session->id);
        $tools = $this->getAvailableTools();
        
        $maxTurns = 3;
        $currentTurn = 0;
        $lastResponse = null;
        $lastToolOutput = null;
        $activeUiType = 'text';

        while ($currentTurn < $maxTurns) {
            $response = $this->callGeminiApi($model, $apiKey, $history, $tools, $compId);
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

            // Execute Intent Library or Action Manager
            $execution = $this->handleToolCalls($session, $toolCalls);
            $toolResults = $execution['results'];
            
            if ($execution['raw_data'] !== null) {
                $lastToolOutput = $execution['raw_data'];
            }
            if ($execution['ui_type'] !== 'text') {
                $activeUiType = $execution['ui_type'];
            }
            
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

    private function handleToolCalls($session, $toolCalls)
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
                    
                    if ($intent === 'TOTAL_DEPOSITS') $output = $this->intentLibrary->getFinancialSummary('Deposit');
                    elseif ($intent === 'TOTAL_WITHDRAWALS') $output = $this->intentLibrary->getFinancialSummary('Withdraw');
                    elseif ($intent === 'CUSTOMER_SEARCH') $output = $this->intentLibrary->searchCustomers($params['term'] ?? '');
                    elseif ($intent === 'LOAN_OVERVIEW') $output = $this->intentLibrary->getLoanOverview();
                    elseif ($intent === 'RECENT_ACTIVITY') $output = $this->intentLibrary->getRecentActivity();
                    elseif ($intent === 'HELP_MENU') $output = $this->intentLibrary->getHelpMenu();
                } 
                elseif ($name === 'prepare_bank_action') {
                    $action = $args['action_type'];
                    $output = $this->actionManager->prepareAction($action, $args['params'] ?? []);
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
                $results[] = ['functionResponse' => ['name' => $name, 'response' => ['error' => 'Tool routing failed.']]];
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
                    'description' => 'Fetches pre-verified reports and data from the bank library.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intent_name' => [
                                'type' => 'string', 
                                'enum' => ['TOTAL_DEPOSITS', 'TOTAL_WITHDRAWALS', 'CUSTOMER_SEARCH', 'LOAN_OVERVIEW', 'RECENT_ACTIVITY', 'HELP_MENU']
                            ],
                            'params' => ['type' => 'object', 'properties' => ['term' => ['type' => 'string']]]
                        ],
                        'required' => ['intent_name']
                    ]
                ],
                [
                    'name' => 'prepare_bank_action',
                    'description' => 'Prepares a sensitive write action for user confirmation.',
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
            ->limit(30)
            ->get();

        $history = [];
        foreach ($messages as $msg) {
            if ($msg->role === 'user' || $msg->role === 'model') {
                $parts = [];
                if ($msg->content) $parts[] = ['text' => $msg->content];
                if ($msg->tool_calls) {
                    $tc = json_decode($msg->tool_calls, true);
                    foreach ($tc as $call) {
                        $parts[] = $call;
                    }
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

    private function callGeminiApi($model, $apiKey, $history, $tools, $compId)
    {
        // Guard: Support only your preferred models
        $validModels = [
            'gemini-2.5-pro',
            'gemini-2.5-flash',
            'gemini-3-flash-preview',
            'gemini-3.1-pro-preview',
            'gemini-3.1-flash-lite-preview',
            'gemini-1.5-flash',
            'gemini-1.5-pro'
        ];
        
        if (!in_array($model, $validModels)) {
            $model = 'gemini-1.5-flash'; 
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemInstruction = "You are the SABS Bank AI Assistant. 
        Context: Company ID is $compId.
        
        MISSION: You are a secure router for bank data. 
        You MUST use `fetch_from_library` for all data requests.
        
        EXAMPLES:
        - User: 'Show me total deposits' -> Call `fetch_from_library(intent_name='TOTAL_DEPOSITS')`
        - User: 'Find John' -> Call `fetch_from_library(intent_name='CUSTOMER_SEARCH', params={'term': 'John'})`
        - User: 'Loan status' -> Call `fetch_from_library(intent_name='LOAN_OVERVIEW')`
        
        STRICT RULES:
        1. NEVER write SQL. 
        2. Always summarize the data you get in under 10 words.
        3. If you can't find a matching intent, call `fetch_from_library(intent_name='HELP_MENU')`.";

        $payload = [
            'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
            'contents' => $history,
            'tools' => $tools,
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'topP' => 0.95,
            ]
        ];

        $response = Http::post($url, $payload);
        
        if ($response->failed()) {
            $errorBody = $response->json();
            Log::error("Gemini API Error Body:", $errorBody);
            $errorMessage = $errorBody['error']['message'] ?? 'Unknown Gemini API Error';
            throw new \Exception("Gemini API Error: " . $errorMessage);
        }
        
        return $response->json();
    }
}
