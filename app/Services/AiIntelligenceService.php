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

        $sixMonthsAgo = Carbon::now()->subMonths(6)->toDateTimeString();
        
        $stats = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('account_number', $customer->account_number)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw("SUM(CASE WHEN name_of_transaction LIKE 'Deposit%' THEN amount ELSE 0 END) as total_deposits"),
                DB::raw("COUNT(CASE WHEN name_of_transaction LIKE 'Deposit%' THEN 1 END) as deposit_count"),
                DB::raw("SUM(CASE WHEN name_of_transaction LIKE 'Withdraw%' THEN amount ELSE 0 END) as total_withdrawals"),
                DB::raw("AVG(CASE WHEN name_of_transaction LIKE 'Deposit%' THEN amount ELSE NULL END) as avg_deposit")
            )
            ->first();

        $dataForAi = [
            'total_deposits' => (float)$stats->total_deposits,
            'deposit_count' => (int)$stats->deposit_count,
            'total_withdrawals' => (float)$stats->total_withdrawals,
            'avg_deposit' => (float)($stats->avg_deposit ?? 0),
            'loan_amount_requested' => $loanAmount
        ];

        $prompt = "Act as a Senior Risk Officer. Analyze this data: " . json_encode($dataForAi) . ". 
        Return JSON: {'score': 0-100, 'color': 'green|yellow|red', 'rationale': 'max 2 sentences'}.";

        $result = $this->callGeminiBasic($prompt);
        
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
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        $lastMonthDate = Carbon::now()->subMonth();

        $stats = DB::table('agent_commissions')
            ->where('agent_id', $agentId)
            ->select(
                DB::raw("SUM(CASE WHEN MONTH(created_at) = $thisMonth AND YEAR(created_at) = $thisYear THEN amount ELSE 0 END) as this_month_earnings"),
                DB::raw("SUM(CASE WHEN MONTH(created_at) = {$lastMonthDate->month} AND YEAR(created_at) = {$lastMonthDate->year} THEN amount ELSE 0 END) as last_month_earnings"),
                DB::raw("COUNT(CASE WHEN MONTH(created_at) = $thisMonth AND YEAR(created_at) = $thisYear THEN 1 END) as this_month_count")
            )
            ->first();

        $prompt = "Act as a Sales Coach for " . ($agent->name ?? 'Agent') . ". Monthly earnings: GHS " . number_format($stats->this_month_earnings, 2) . ". 
        Provide 2 motivating sentences. No markdown.";

        $result = $this->callGeminiBasic($prompt);
        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => 'Growth Coach',
                'value' => number_format($stats->this_month_earnings, 2),
                'suffix' => 'GHS Earned',
                'details' => $result['content'] ?? 'Keep pushing!'
            ],
            'caption' => 'Your Performance Brief:'
        ];
    }

    /**
     * Feature 5: Executive Briefing - Strategic Boardroom View
     */
    public function getExecutiveBriefing()
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // --- 1. SYSTEM LIQUIDITY & POSITION (Improved Matching) ---
        $baseQuery = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', 'NOT LIKE', '%reversal%')
            ->where('description', 'NOT LIKE', '%reversal%');

        $totalPoolDeposits = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Deposit%')->sum('amount');
        $totalPoolWithdrawals = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Withdraw%')->sum('amount');
        $totalLoanRepayments = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Loan Repayment%')->sum('amount');
        
        $totalFeesCharged = (float)(clone $baseQuery)->where(function($q) {
            $q->where('name_of_transaction', 'LIKE', '%fee%')
              ->orWhere('name_of_transaction', 'LIKE', '%charge%')
              ->orWhere('name_of_transaction', 'LIKE', '%sms%')
              ->orWhere('name_of_transaction', 'LIKE', '%maintenance%');
        })->sum('amount');

        $actualCashInHand = ($totalPoolDeposits + $totalLoanRepayments) - $totalPoolWithdrawals;
        $totalSavingsLiability = $totalPoolDeposits - ($totalPoolWithdrawals + $totalFeesCharged);
        $netSystemPosition = $actualCashInHand - $totalSavingsLiability;

        // --- MTD PERFORMANCE ---
        $monthlyDeposits = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'LIKE', 'Deposit%')->where('created_at', '>=', $monthStart)->sum('amount');
        $monthlyWithdrawals = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'LIKE', 'Withdraw%')->where('created_at', '>=', $monthStart)->sum('amount');
        $monthlyRepayments = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'LIKE', 'Loan Repayment%')->where('created_at', '>=', $monthStart)->sum('amount');
        
        $monthlyDisbursed = DB::table('loan_applications')
            ->where('comp_id', $this->compId)
            ->whereIn('status', ['active', 'disbursed', 'repaid'])
            ->where('updated_at', '>=', $monthStart)
            ->sum('amount');

        // --- RISK ---
        $arrears = DB::table('loan_repayment_schedules')
            ->where('comp_id', $this->compId)
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'paid')
            ->select(
                DB::raw("COUNT(*) as count"),
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_amount")
            )->first();

        $dormantCount = DB::table('nobs_user_account_numbers')->where('comp_id', $this->compId)->where('account_status', 'dormant')->count();

        $fullSystemData = [
            'liquidity' => [
                'net_system_position' => $netSystemPosition,
                'actual_cash_on_hand' => $actualCashInHand,
                'savings_liability' => $totalSavingsLiability
            ],
            'performance_mtd' => [
                'deposits' => $monthlyDeposits,
                'withdrawals' => $monthlyWithdrawals,
                'repayments' => $monthlyRepayments,
                'disbursed' => $monthlyDisbursed
            ],
            'risk' => [
                'unpaid_balance' => $arrears->total_amount,
                'unpaid_cases' => $arrears->count,
                'dormant_accounts' => $dormantCount
            ]
        ];

        $prompt = "Act as a CFO. Analyze this data: " . json_encode($fullSystemData) . ". 
        1. Compare Net Position vs Arrears. 2. Comment on monthly cash flow.
        MANDATORY: Return a JSON object with: 
        'strategy': 3-sentence executive advice.
        'caption': A warm conversational intro. No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? ($brief['content'] ?? 'Strategic review based on monthly cash flow.');
        $caption = $brief['caption'] ?? 'Executive Financial Intelligence Briefing:';

        $metadata = [
            ['Net System Position' => number_format($netSystemPosition, 2) . ' GHS'],
            ['Actual Cash Pool' => number_format($actualCashInHand, 2) . ' GHS'],
            ['Monthly Deposits' => number_format($monthlyDeposits, 2) . ' GHS'],
            ['Monthly Withdrawals' => number_format($monthlyWithdrawals, 2) . ' GHS'],
            ['Monthly Repayments' => number_format($monthlyRepayments, 2) . ' GHS'],
            ['MTD Disbursed' => number_format($monthlyDisbursed, 2) . ' GHS'],
            ['Unpaid Loans' => number_format($arrears->total_amount, 2) . ' GHS'],
            ['Dormant Accounts' => (int)$dormantCount],
            ['Strategic Review' => $strategyText]
        ];

        // Keep core liquidity always, filter other zeros
        $filteredMetadata = array_values(array_filter($metadata, function($item) {
            $key = array_keys($item)[0];
            $val = array_values($item)[0];
            if ($key === 'Net System Position' || $key === 'Actual Cash Pool') return true;
            return !($val === '0.00 GHS' || $val === 0 || $val === '0' || empty($val));
        }));

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $filteredMetadata,
            'caption' => $caption
        ];
    }

    private function callGeminiBasic($prompt)
    {
        if (!$this->apiKey) return ['content' => 'API Key missing.'];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'maxOutputTokens' => 300]
        ];

        try {
            $response = Http::post($url, $payload);
            
            if ($response->failed()) {
                Log::error("Gemini Basic Error: " . $response->body());
                return ['content' => "Service unavailable (Status " . $response->status() . ")."];
            }

            $json = $response->json();
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean markdown and isolate JSON
            $cleanText = preg_replace('/^```json\s*|```\s*$/m', '', trim($text));
            $firstBrace = strpos($cleanText, '{');
            $lastBrace = strrpos($cleanText, '}');

            if ($firstBrace !== false && $lastBrace !== false) {
                $potentialJson = substr($cleanText, $firstBrace, $lastBrace - $firstBrace + 1);
                $decoded = json_decode($potentialJson, true);
                if ($decoded) return $decoded;
            }
            
            return ['content' => strip_tags($text)];
        } catch (\Throwable $e) {
            Log::error("Intelligence Service Error: " . $e->getMessage());
            return ['content' => 'Intelligence analysis failed.'];
        }
    }
}
