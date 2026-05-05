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
     * Supports granular periods: daily, weekly, monthly, yearly, alltime
     */
    public function getExecutiveBriefing($period = 'monthly')
    {
        $today = Carbon::now();
        $startDate = null;
        $label = "";

        switch($period) {
            case 'daily':
                $startDate = Carbon::today();
                $label = "Today (" . date('d M') . ")";
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek();
                $label = "This Week";
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear();
                $label = "This Year (" . date('Y') . ")";
                break;
            case 'alltime':
                $startDate = Carbon::parse('2000-01-01');
                $label = "All Time";
                break;
            case 'monthly':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $label = "This Month (" . date('M Y') . ")";
                break;
        }

        // --- 1. PHYSICAL ASSETS (Unified Financial Pipeline) ---
        $officeCash = (float)DB::table('treasury_accounts')->where('comp_id', $this->compId)->where('account_type', 'safe')->sum('balance');
        $bankReserves = (float)DB::table('treasury_accounts')->where('comp_id', $this->compId)->where('account_type', 'bank')->sum('balance');
        $agentFieldCash = (float)DB::table('agent_pouch_ledger')->where('comp_id', $this->compId)->sum('current_balance');
        
        $totalPhysicalCash = $officeCash + $bankReserves + $agentFieldCash;

        // --- 2. DIGITAL LIABILITIES (Customer Savings) ---
        // CRITICAL FIX: Use the actual balances from nobs_user_account_numbers instead of summing history.
        // History summing is prone to errors (LIKE 'Deposit%', reversals, historical missing data).
        $totalSavingsLiability = (float)DB::table('nobs_user_account_numbers')
            ->where('comp_id', $this->compId)
            ->where('account_type', 'NOT LIKE', '%Loan%')
            ->sum('balance');

        $netSystemPosition = $totalPhysicalCash - $totalSavingsLiability;

        // --- 3. PERIOD PERFORMANCE ---
        $periodQuery = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->whereBetween('created_at', [$startDate, $today]);
        
        $periodDeposits = (float)(clone $periodQuery)->where('name_of_transaction', 'LIKE', 'Deposit%')->sum('amount');
        $periodWithdrawals = (float)(clone $periodQuery)->where('name_of_transaction', 'LIKE', 'Withdraw%')->sum('amount');
        $periodRepayments = (float)(clone $periodQuery)->where('name_of_transaction', 'LIKE', 'Loan Repayment%')->sum('amount');
        
        $periodDisbursed = (float)DB::table('loan_applications')
            ->where('comp_id', $this->compId)
            ->whereIn('status', ['active', 'disbursed', 'repaid'])
            ->whereBetween('updated_at', [$startDate, $today])
            ->sum('amount');

        $totalCustomers = DB::table('nobs_registration')->where('comp_id', $this->compId)->count();
        $totalPortfolioValue = DB::table('loan_applications')->where('comp_id', $this->compId)->whereIn('status', ['active', 'disbursed'])->sum('amount');

        $fullSystemData = [
            'period_label' => $label,
            'summary' => [
                'liquid_cash_reserves' => $officeCash + $bankReserves,
                'agent_field_debt' => $agentFieldCash,
                'customer_savings_liability' => $totalSavingsLiability,
                'true_net_position' => $netSystemPosition,
                'period_deposits' => $periodDeposits,
                'period_withdrawals' => $periodWithdrawals,
                'period_repayments' => $periodRepayments,
                'period_disbursements' => $periodDisbursed,
                'total_customers' => $totalCustomers,
                'portfolio_value' => $totalPortfolioValue
            ]
        ];

        // MANDATORY: FULL DESCRIPTIVE PROMPT
        $prompt = "Act as the Chief Financial Officer. Summarize this bank performance for $label.
        DATA: " . json_encode($fullSystemData['summary']) . ".
        TASK:
        1. Write a 3-sentence conversational 'caption' summarizing key metrics. Focus on the relationship between Liquid Cash, Field Debt, and Savings Liability.
        2. Write a 3-sentence 'strategy' key providing executive advice on liquidity management.
        Return JSON object with keys 'caption' and 'strategy'. No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? 'Strategic review pending data synchronization.';
        $caption = $brief['caption'] ?? "Executive Financial Intelligence Briefing for $label:";

        $metadata = [
            ['Office/Bank Reserves' => number_format($officeCash + $bankReserves, 2) . ' GHS'],
            ['Agent Field Cash' => number_format($agentFieldCash, 2) . ' GHS'],
            ['Savings Liability' => number_format($totalSavingsLiability, 2) . ' GHS'],
            ['True Net Position' => number_format($netSystemPosition, 2) . ' GHS'],
            ['Portfolio Value' => number_format($totalPortfolioValue, 2) . ' GHS'],
            ['Total Customers' => (int)$totalCustomers],
            ["Deposits ($period)" => number_format($periodDeposits, 2) . ' GHS'],
            ["Withdrawals ($period)" => number_format($periodWithdrawals, 2) . ' GHS'],
            ["Repayments ($period)" => number_format($periodRepayments, 2) . ' GHS'],
            ["Disbursed ($period)" => number_format($periodDisbursed, 2) . ' GHS'],
            ['Strategic Review' => $strategyText]
        ];

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $metadata,
            'caption' => $caption
        ];
    }

    private function callGeminiBasic($prompt)
    {
        if (!$this->apiKey) return ['content' => 'API Key missing.'];

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        $payload = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'maxOutputTokens' => 600]
        ];

        try {
            $response = Http::post($url, $payload);
            
            if ($response->failed()) {
                Log::error("Gemini Basic Error: " . $response->body());
                return ['content' => "Service unavailable (Status " . $response->status() . ")."];
            }

            $json = $response->json();
            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
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
