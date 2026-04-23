<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAgentController extends Controller
{
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

            $prompt = $request->input('message');
            $sessionId = $request->input('session_id');
            $model = $request->input('model', 'gemini-3-flash-preview');
            $requestApiKey = $request->input('api_key');

            if (empty($prompt)) {
                return response()->json(['success' => false, 'message' => 'Message is required'], 400);
            }

            $apiKey = $requestApiKey ?: env('GOOGLE_AI_API_KEY');
            if (!$apiKey) {
                return response()->json(['success' => false, 'message' => 'AI API Key is missing. Please check your AI Settings.'], 400);
            }

            $session = $this->getOrCreateSession($user, $sessionId, $model);
            $this->storeMessage($session->id, 'user', $prompt);

            // Process with Gemini and get enriched result
            $result = $this->processWithGemini($session, $prompt, $model, $apiKey);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'response' => $result['response'],
                'ui_type' => $result['ui_type'],
                'ui_metadata' => $result['ui_metadata']
            ]);

        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AI Error: ' . $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
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
        return DB::table('ai_messages')->insertGetId([
            'session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
            'tool_calls' => $toolCalls ? (is_string($toolCalls) ? $toolCalls : json_encode($toolCalls)) : null,
            'ui_type' => $uiType,
            'ui_metadata' => $uiMetadata ? (is_string($uiMetadata) ? $uiMetadata : json_encode($uiMetadata)) : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function processWithGemini($session, $prompt, $model, $apiKey)
    {
        $history = $this->getHistory($session->id);
        $tools = $this->getAvailableTools();
        
        $maxTurns = 3;
        $currentTurn = 0;
        $lastResponse = null;
        $lastToolOutput = null;
        $activeUiType = 'text';

        while ($currentTurn < $maxTurns) {
            $response = $this->callGeminiApi($model, $apiKey, $history, $tools);
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

            // Execute Tools and detect UI type
            $execution = $this->handleToolCalls($session, $toolCalls);
            $toolResults = $execution['results'];
            
            // Only update metadata if we actually got new data
            if ($execution['raw_data'] !== null) {
                $lastToolOutput = $execution['raw_data'];
            }
            
            // Only update UI type if it's more specific than 'text'
            if ($execution['ui_type'] !== 'text') {
                $activeUiType = $execution['ui_type'];
            }
            
            // Store the tool execution in DB history
            foreach ($toolResults as $tr) {
                $this->storeMessage(
                    $session->id, 
                    'tool', 
                    json_encode($tr['functionResponse']['response']), 
                    null, 
                    $activeUiType, 
                    null
                );
                // Also update the storeMessage to accept tool_call_id if needed, 
                // but here we just need to ensure the role 'tool' is recorded.
                // Looking at the DB schema, tool_call_id is a column.
                DB::table('ai_messages')
                    ->where('session_id', $session->id)
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->update(['tool_call_id' => $tr['functionResponse']['name']]);
            }

            $history[] = $candidate; 
            $history[] = [
                'role' => 'function',
                'parts' => $toolResults
            ];

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
            $methodName = 'tool_' . $func['name'];
            
            // Map tool names to UI types
            if ($func['name'] === 'execute_analytical_query') $uiType = 'data_table';
            if ($func['name'] === 'search_customer') $uiType = 'customer_card';
            if ($func['name'] === 'process_deposit') $uiType = 'action_result';

            try {
                if (method_exists($this, $methodName)) {
                    $output = $this->$methodName($session, $func['args'] ?? []);
                    $rawData = $output; // Capture raw data for metadata
                    
                    $results[] = [
                        'functionResponse' => [
                            'name' => $func['name'],
                            'response' => ['name' => $func['name'], 'content' => $output]
                        ]
                    ];
                } else {
                    $results[] = [
                        'functionResponse' => [
                            'name' => $func['name'],
                            'response' => ['error' => "Tool '{$func['name']}' not implemented."]
                        ]
                    ];
                }
            } catch (\Throwable $e) {
                $results[] = [
                    'functionResponse' => [
                        'name' => $func['name'],
                        'response' => ['error' => $e->getMessage()]
                    ]
                ];
            }
        }
        return ['results' => $results, 'raw_data' => $rawData, 'ui_type' => $uiType];
    }

    /**
     * SECURE SQL TOOL
     */
    private function tool_execute_analytical_query($session, $args)
    {
        $query = $args['query'] ?? null;
        if (!$query) return "Error: No query provided.";

        $forbidden = ['update', 'delete', 'insert', 'drop', 'truncate', 'alter', 'grant', 'revoke'];
        $lowerQuery = strtolower($query);
        foreach ($forbidden as $word) {
            if (strpos($lowerQuery, $word) !== false) {
                return "Security Error: Query contains forbidden keyword '$word'. Only SELECT is allowed.";
            }
        }

        $compId = $session->comp_id;
        if (strpos($lowerQuery, 'comp_id') === false) {
             if (preg_match('/where/i', $query)) {
                $query = preg_replace('/where/i', "WHERE comp_id = $compId AND (", $query) . ")";
            } else {
                $query .= " WHERE comp_id = $compId";
            }
        }

        try {
            return DB::connection('mysql_readonly')->select($query);
        } catch (\Throwable $e) {
            return "SQL Error: " . $e->getMessage();
        }
    }

    private function tool_search_customer($session, $args)
    {
        $term = $args['search_term'] ?? null;
        if (!$term) return "Error: No search term.";

        return DB::table('nobs_registration')
            ->where('comp_id', $session->comp_id)
            ->where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%$term%")
                  ->orWhere('surname', 'LIKE', "%$term%")
                  ->orWhere('phone_number', 'LIKE', "%$term%")
                  ->orWhere('account_number', 'LIKE', "%$term%");
            })
            ->limit(5)
            ->get();
    }

    private function tool_process_deposit($session, $args)
    {
        $account = $args['account_number'] ?? null;
        $amount = $args['amount'] ?? null;
        if (!$account || !$amount) return "Error: Missing account or amount.";

        $apiController = new ApiUsersController();
        $request = new Request([
            'account_number' => $account,
            'amount' => $amount,
            'users' => auth('api')->id(),
            'comp_id' => $session->comp_id,
            'name_of_transaction' => 'Deposit'
        ]);

        $res = $apiController->deposittransaction($request);
        return [
            'status' => $res == 200 ? 'success' : 'failed',
            'message' => $res == 200 ? 'Deposit successful' : 'Transaction failed with code ' . $res,
            'details' => ['account' => $account, 'amount' => $amount]
        ];
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
                $history[] = [
                    'role' => $msg->role,
                    'parts' => $parts
                ];
            } else if ($msg->role === 'tool') {
                $history[] = [
                    'role' => 'function',
                    'parts' => [
                        [
                            'functionResponse' => [
                                'name' => $msg->tool_call_id, // This should store the function name
                                'response' => json_decode($msg->content, true)
                            ]
                        ]
                    ]
                ];
            }
        }
        return $history;
    }

    private function getAvailableTools()
    {
        return [
            [
                'function_declarations' => [
                    [
                        'name' => 'execute_analytical_query',
                        'description' => 'Executes a READ-ONLY SQL SELECT query for BI and data analysis. Use this for reports, totals, and counts.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => ['type' => 'string', 'description' => 'The SQL query to execute.']
                            ],
                            'required' => ['query']
                        ]
                    ],
                    [
                        'name' => 'search_customer',
                        'description' => 'Search for a customer by name, phone, or account number.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'search_term' => ['type' => 'string']
                            ],
                            'required' => ['search_term']
                        ]
                    ],
                    [
                        'name' => 'process_deposit',
                        'description' => 'Executes a financial deposit into a customer account.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'account_number' => ['type' => 'string'],
                                'amount' => ['type' => 'number']
                            ],
                            'required' => ['account_number', 'amount']
                        ]
                    ]
                ]
            ]
        ];
    }

    private function callGeminiApi($model, $apiKey, $history, $tools)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'system_instruction' => [
                'parts' => [
                    'text' => "You are SABS AI Assistant. Context: Company ID is " . auth('api')->user()->comp_id . ". 
                    Security: Only SELECT queries. Always filter by comp_id.
                    Tone: Professional but friendly. 
                    Rule: When a tool returns data, ALWAYS summarize the findings in your text response. 
                    NEVER just say 'Action completed'. Explain what you found or what you did.
                    For SQL queries, if the user asks for 'today', use CURDATE()."
                ]
            ],
            'contents' => $history,
            'tools' => $tools,
            'generationConfig' => [
                'temperature' => 0.1,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $payload);
        if ($response->failed()) {
            throw new \Exception("Gemini API Error: " . ($response->json()['error']['message'] ?? $response->body()));
        }
        return $response->json();
    }
}
