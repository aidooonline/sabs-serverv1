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
        $today = Carbon::now()->endOfDay();
        $startDate = null;
        $label = "";

        switch($period) {
            case 'daily':
                $startDate = Carbon::today()->startOfDay();
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
                $startDate = Carbon::parse('2000-01-01'); // Beginning of time
                $label = "All Time";
                break;
            case 'monthly':
            default:
                $startDate = Carbon::now()->startOfMonth();
                $label = "This Month (" . date('M Y') . ")";
                break;
        }

        $startStr = $startDate->toDateTimeString();
        $endStr = $today->toDateTimeString();

        // --- 1. SYSTEM LIQUIDITY & POSITION (Improved Matching) ---
        $baseQuery = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            // Removed amount limit to ensure all large transactions are captured
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

        // Verified Formulas
        $actualCashInHand = ($totalPoolDeposits + $totalLoanRepayments) - $totalPoolWithdrawals;
        $totalSavingsLiability = $totalPoolDeposits - ($totalPoolWithdrawals + $totalFeesCharged);
        $netSystemPosition = $actualCashInHand - $totalSavingsLiability;

        // --- 2. PERIOD PERFORMANCE ---
        $periodDeposits = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Deposit%')->whereBetween('created_at', [$startStr, $endStr])->sum('amount');
        $periodWithdrawals = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Withdraw%')->whereBetween('created_at', [$startStr, $endStr])->sum('amount');
        $periodRepayments = (float)(clone $baseQuery)->where('name_of_transaction', 'LIKE', 'Loan Repayment%')->whereBetween('created_at', [$startStr, $endStr])->sum('amount');
        
        $periodDisbursed = (float)DB::table('loan_applications')
            ->where('comp_id', $this->compId)
            ->whereIn('status', ['active', 'disbursed', 'repaid'])
            ->whereBetween('updated_at', [$startStr, $endStr])
            ->sum('amount');

        // --- 3. TOTAL PORTFOLIO & CUSTOMERS ---
        $totalCustomers = DB::table('nobs_registration')->where('comp_id', $this->compId)->count();
        $totalPortfolioValue = DB::table('loan_applications')->where('comp_id', $this->compId)->whereIn('status', ['active', 'disbursed'])->sum('amount');

        // --- 4. RISK ---
        $arrears = DB::table('loan_repayment_schedules')
            ->where('comp_id', $this->compId)
            ->where('due_date', '<', date('Y-m-d'))
            ->where('status', '!=', 'paid')
            ->select(
                DB::raw("COUNT(*) as count"),
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_amount")
            )->first();

        $dormantCount = DB::table('nobs_user_account_numbers')->where('comp_id', $this->compId)->where('account_status', 'dormant')->count();

        $fullSystemData = [
            'period_label' => $label,
            'liquidity' => [
                'net_system_position' => $netSystemPosition,
                'actual_cash_on_hand' => $actualCashInHand,
                'savings_liability' => $totalSavingsLiability
            ],
            'period_activity' => [
                'deposits' => $periodDeposits,
                'withdrawals' => $periodWithdrawals,
                'repayments' => $periodRepayments,
                'disbursed' => $periodDisbursed
            ],
            'portfolio' => [
                'total_customers' => $totalCustomers,
                'portfolio_value' => $totalPortfolioValue
            ],
            'risk' => [
                'unpaid_balance' => $arrears->total_amount,
                'unpaid_cases' => $arrears->count,
                'dormant_accounts' => $dormantCount
            ]
        ];

        $prompt = "Act as a CFO. Analyze this bank data: " . json_encode($fullSystemData) . ". 
        Analyze the period $label. Compare performance and risk.
        MANDATORY: Return a JSON object with: 
        'strategy': 3-sentence executive advice.
        'caption': A warm conversational intro. No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? ($brief['content'] ?? 'Strategic review pending data synchronization.');
        $caption = $brief['caption'] ?? "Executive Financial Intelligence Briefing for $label:";

        $metadata = [
            ['Net System Position' => number_format($netSystemPosition, 2) . ' GHS'],
            ['Actual Cash Pool' => number_format($actualCashInHand, 2) . ' GHS'],
            ['Total Customers' => (int)$totalCustomers],
            ['Portfolio Value' => number_format($totalPortfolioValue, 2) . ' GHS'],
            ["Deposits ($period)" => number_format($periodDeposits, 2) . ' GHS'],
            ["Withdrawals ($period)" => number_format($periodWithdrawals, 2) . ' GHS'],
            ["Disbursed ($period)" => number_format($periodDisbursed, 2) . ' GHS'],
            ['Unpaid Loans' => number_format($arrears->total_amount, 2) . ' GHS'],
            ['Dormant Accounts' => (int)$dormantCount],
            ['Strategic Review' => $strategyText]
        ];

        // Keep core liquidity and customers always, filter other zeros
        $filteredMetadata = array_values(array_filter($metadata, function($item) {
            $key = array_keys($item)[0];
            $val = array_values($item)[0];
            if ($key === 'Net System Position' || $key === 'Actual Cash Pool' || $key === 'Total Customers') return true;
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
            'generationConfig' => ['temperature' => 0.7, 'topP' => 0.95, 'maxOutputTokens' => 400]
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
