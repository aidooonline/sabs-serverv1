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

    public function __construct($compId)
    {
        $this->compId = $compId;
        $this->intentLibrary = new AiIntentLibrary($compId);
    }

    /**
     * Feature 1: Predictive Risk Shield
     */
    public function getRiskAnalysis($customerId, $loanAmount)
    {
        $customer = DB::table('nobs_registration')->where('id', $customerId)->first();
        if (!$customer) return ['error' => 'Customer not found'];

        // Gather 6 months of transaction history
        $sixMonthsAgo = Carbon::now()->subMonths(6)->toDateTimeString();
        $txs = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('account_number', $customer->account_number)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select('name_of_transaction', 'amount', 'created_at')
            ->get();

        $stats = [
            'total_deposits' => $txs->where('name_of_transaction', 'Deposit')->sum('amount'),
            'deposit_count' => $txs->where('name_of_transaction', 'Deposit')->count(),
            'total_withdrawals' => $txs->where('name_of_transaction', 'Withdraw')->sum('amount'),
            'avg_deposit' => $txs->where('name_of_transaction', 'Deposit')->avg('amount') ?: 0,
            'loan_amount_requested' => $loanAmount
        ];

        $prompt = "Act as a Senior Risk Officer. Analyze this customer data for a loan request of GHS $loanAmount: " . json_encode($stats) . ". 
        Provide a JSON response with: 'score' (0-100), 'color' (green, yellow, red), and 'rationale' (max 2 sentences).";

        return $this->callGeminiBasic($prompt);
    }

    /**
     * Feature 3: Agent Growth Coach
     */
    public function getAgentCoaching($agentId)
    {
        $agent = DB::table('users')->where('id', $agentId)->first();
        
        // Performance this month vs last
        $thisMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonth()->month;

        $stats = DB::table('agent_commissions')
            ->where('agent_id', $agentId)
            ->select(
                DB::raw("SUM(CASE WHEN MONTH(created_at) = $thisMonth THEN amount ELSE 0 END) as this_month_earnings"),
                DB::raw("SUM(CASE WHEN MONTH(created_at) = $lastMonth THEN amount ELSE 0 END) as last_month_earnings"),
                DB::raw("COUNT(CASE WHEN MONTH(created_at) = $thisMonth THEN 1 END) as this_month_count")
            )
            ->first();

        $prompt = "Act as a Motivational Sales Coach for field agent " . ($agent->name ?? 'Agent') . ". 
        Monthly Stats: " . json_encode($stats) . ". 
        Provide a warm, highly motivating 2-sentence briefing on how they can earn more today. No markdown.";

        $result = $this->callGeminiBasic($prompt);
        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => 'Growth Coach',
                'value' => number_format($stats->this_month_earnings, 2),
                'suffix' => 'GHS Earned',
                'details' => $result['rationale'] ?? $result['content'] ?? 'Keep pushing!'
            ],
            'caption' => 'Your Personalized Performance Brief:'
        ];
    }

    /**
     * Feature 5: Executive Briefing
     */
    public function getExecutiveBriefing()
    {
        $liquidity = $this->intentLibrary->getSystemLiquidity();
        $summary = $this->intentLibrary->getDailySummary(date('Y-m-d'));
        $arrears = $this->intentLibrary->getArrearsList();

        $data = [
            'liquidity' => $liquidity['ui_metadata'],
            'today_activity' => $summary['ui_metadata'],
            'top_arrears' => count($arrears['ui_metadata'])
        ];

        $prompt = "Act as a CFO. Summarize this bank health data: " . json_encode($data) . ". 
        Provide a strategic 3-sentence summary for the CEO. Focus on liquidity and risk. No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        
        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => [
                ['Metric' => 'Net Position', 'Value' => $liquidity['ui_metadata']['value'] . ' GHS'],
                ['Metric' => 'Arrears Count', 'Value' => $data['top_arrears']],
                ['Metric' => 'Strategy', 'Value' => $brief['rationale'] ?? $brief['content'] ?? 'Monitor arrears.']
            ],
            'caption' => 'Strategic Executive Briefing:'
        ];
    }

    private function callGeminiBasic($prompt)
    {
        $apiKey = env('GOOGLE_AI_API_KEY');
        $model = 'gemini-1.5-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'maxOutputTokens' => 200]
        ];

        try {
            $response = Http::post($url, $payload);
            $json = $response->json();
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Try to parse as JSON if it looks like it
            if (strpos($text, '{') !== false) {
                $cleanJson = substr($text, strpos($text, '{'), strrpos($text, '}') - strpos($text, '{') + 1);
                $decoded = json_decode($cleanJson, true);
                if ($decoded) return $decoded;
            }
            
            return ['content' => strip_tags($text)];
        } catch (\Throwable $e) {
            Log::error("Intelligence Service Gemini Error: " . $e->getMessage());
            return ['content' => 'Data intelligence is temporarily unavailable.'];
        }
    }
}
