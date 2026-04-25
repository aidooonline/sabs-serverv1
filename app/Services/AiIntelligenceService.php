<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiIntelligenceService
{
    private $compId;
    private $intentLibrary;
    private $apiKey;
    private $model;

    public function __construct($compId, $apiKey = null, $model = 'gemini-1.5-flash')
    {
        $this->compId = $compId;
        $this->intentLibrary = new AiIntentLibrary($compId);
        $this->apiKey = $apiKey ?: env('GOOGLE_AI_API_KEY');
        $this->model = $model ?: 'gemini-1.5-flash';
    }

    /**
     * Feature 1: Predictive Risk Shield
     */
    public function getRiskAnalysis($customerId, $loanAmount)
    {
        $customer = DB::table('nobs_registration')->where('id', $customerId)->first();
        if (!$customer) return ['score' => 'N/A', 'color' => 'grey', 'rationale' => 'Customer profile not found.'];

        // Optimize: Use DB Aggregates instead of fetching all rows (Prevents Memory Bloat)
        $sixMonthsAgo = Carbon::now()->subMonths(6)->toDateTimeString();
        
        $stats = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('account_number', $customer->account_number)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Deposit' THEN amount ELSE 0 END) as total_deposits"),
                DB::raw("COUNT(CASE WHEN name_of_transaction = 'Deposit' THEN 1 END) as deposit_count"),
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Withdraw' THEN amount ELSE 0 END) as total_withdrawals"),
                DB::raw("AVG(CASE WHEN name_of_transaction = 'Deposit' THEN amount ELSE NULL END) as avg_deposit")
            )
            ->first();

        $dataForAi = [
            'total_deposits' => (float)$stats->total_deposits,
            'deposit_count' => (int)$stats->deposit_count,
            'total_withdrawals' => (float)$stats->total_withdrawals,
            'avg_deposit' => (float)($stats->avg_deposit ?? 0),
            'loan_amount_requested' => $loanAmount
        ];

        $prompt = "Act as a Senior Risk Officer. Analyze this customer transaction data (Last 6 Months) for a loan request of GHS $loanAmount: " . json_encode($dataForAi) . ". 
        Provide a JSON response with: 'score' (0-100), 'color' (green, yellow, red), and 'rationale' (max 2 sentences explaining the decision).";

        $result = $this->callGeminiBasic($prompt);
        
        // Ensure keys exist for frontend
        return [
            'score' => $result['score'] ?? 'N/A',
            'color' => $result['color'] ?? 'grey',
            'rationale' => $result['rationale'] ?? ($result['content'] ?? 'Risk analysis partially unavailable.')
        ];
    }

    /**
     * Feature 3: Agent Growth Coach
     */
    public function getAgentCoaching($agentId)
    {
        $agent = DB::table('users')->where('id', $agentId)->first();
        
        // Performance this month vs last (Fixed: Added Year Check to avoid picking old years)
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        $lastMonthDate = Carbon::now()->subMonth();
        $lastMonth = $lastMonthDate->month;
        $lastMonthYear = $lastMonthDate->year;

        $stats = DB::table('agent_commissions')
            ->where('agent_id', $agentId)
            ->select(
                DB::raw("SUM(CASE WHEN MONTH(created_at) = $thisMonth AND YEAR(created_at) = $thisYear THEN amount ELSE 0 END) as this_month_earnings"),
                DB::raw("SUM(CASE WHEN MONTH(created_at) = $lastMonth AND YEAR(created_at) = $lastMonthYear THEN amount ELSE 0 END) as last_month_earnings"),
                DB::raw("COUNT(CASE WHEN MONTH(created_at) = $thisMonth AND YEAR(created_at) = $thisYear THEN 1 END) as this_month_count")
            )
            ->first();

        $prompt = "Act as a Motivational Sales Coach for field agent " . ($agent->name ?? 'Agent') . ". 
        Earnings this month: GHS " . number_format($stats->this_month_earnings, 2) . " (Count: $stats->this_month_count). 
        Earnings last month: GHS " . number_format($stats->last_month_earnings, 2) . ".
        Provide a warm, highly motivating 2-sentence briefing on how they can earn more today. No markdown.";

        $result = $this->callGeminiBasic($prompt);
        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => 'Growth Coach',
                'value' => number_format($stats->this_month_earnings, 2),
                'suffix' => 'GHS Earned',
                'details' => $result['rationale'] ?? $result['content'] ?? 'Keep pushing! Every deposit counts toward your bonus.'
            ],
            'caption' => 'Your Performance Brief:'
        ];
    }

    /**
     * Feature 5: Executive Briefing
     */
    public function getExecutiveBriefing()
    {
        $liquidity = $this->intentLibrary->getSystemLiquidity();
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $monthStart = date('Y-m-01');

        // Detailed Deposit Metrics
        $depositsToday = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Deposit')->whereDate('created_at', $today)->sum('amount');
        $depositsYesterday = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Deposit')->whereDate('created_at', $yesterday)->sum('amount');
        $depositsMonth = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Deposit')->where('created_at', '>=', $monthStart)->sum('amount');

        // Granular Arrears Data
        $arrears = DB::table('loan_repayment_schedules')
            ->where('comp_id', $this->compId)
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'paid')
            ->select(
                DB::raw("COUNT(*) as count"),
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_amount")
            )->first();

        $data = [
            'liquidity' => $liquidity['ui_metadata'],
            'deposits' => [
                'today' => (float)$depositsToday,
                'yesterday' => (float)$depositsYesterday,
                'this_month' => (float)$depositsMonth
            ],
            'arrears' => [
                'count' => (int)$arrears->count,
                'amount' => (float)$arrears->total_amount
            ]
        ];

        $prompt = "Act as a CFO. Summarize this bank health data: " . json_encode($data) . ". 
        Provide a JSON response with a single key 'strategy' containing a strategic 3-sentence summary for the CEO. Mention if deposits are growing vs yesterday and assess the arrears risk (GHS " . number_format($data['arrears']['amount'], 2) . "). No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? ($brief['content'] ?? 'Liquidity is stable. Monitor arrears daily for risk mitigation.');

        // Build filtered metadata
        $rawMetadata = [
            ['Net Liquidity' => number_format((float)str_replace(',', '', $liquidity['ui_metadata']['value']), 2) . ' GHS'],
            ['Deposits Today' => number_format($data['deposits']['today'], 2) . ' GHS'],
            ['Deposits Month' => number_format($data['deposits']['this_month'], 2) . ' GHS'],
            ['Unpaid Loans Balance' => number_format($data['arrears']['amount'], 2) . ' GHS'],
            ['Unpaid Cases' => $data['arrears']['count']],
            ['Strategic Advice' => $strategyText]
        ];

        // Filter out zero/empty values completely to prevent empty cards
        $filteredMetadata = array_values(array_filter($rawMetadata, function($item) {
            $val = array_values($item)[0];
            return !($val === '0.00 GHS' || $val === 0 || $val === null || $val === '');
        }));

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $filteredMetadata,
            'caption' => 'Executive Briefing Summary:'
        ];
    }

    private function callGeminiBasic($prompt)
    {
        if (!$this->apiKey) {
            return ['content' => 'AI API Key is missing. Please check settings.'];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'maxOutputTokens' => 200]
        ];

        try {
            $response = Http::post($url, $payload);
            
            if ($response->failed()) {
                $status = $response->status();
                $errorBody = $response->body();
                $size = strlen(json_encode($payload));
                Log::error("Intelligence Gemini API Error [$status]: Size $size bytes. Response: $errorBody");
                return ['content' => "Intelligence service unavailable (Status $status)."];
            }

            $json = $response->json();
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // SUPER ROBUST JSON EXTRACTOR
            // 1. Remove markdown code blocks if present
            $cleanText = preg_replace('/^```json\s*|```\s*$/m', '', trim($text));
            
            // 2. Find the first '{' and last '}' to isolate the JSON object
            $firstBrace = strpos($cleanText, '{');
            $lastBrace = strrpos($cleanText, '}');

            if ($firstBrace !== false && $lastBrace !== false) {
                $potentialJson = substr($cleanText, $firstBrace, $lastBrace - $firstBrace + 1);
                $decoded = json_decode($potentialJson, true);
                if ($decoded) return $decoded;
            }
            
            return ['content' => strip_tags($text)];
        } catch (\Throwable $e) {
            Log::error("Intelligence Service Gemini Error: " . $e->getMessage());
            return ['content' => 'Data intelligence is temporarily unavailable.'];
        }
    }
}
