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
     * Feature 5: Executive Briefing - Strategic Boardroom View
     */
    public function getExecutiveBriefing()
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // 1. LIQUIDITY & TRUE CASH-AT-HAND (Aggregated since beginning of time)
        $baseQuery = DB::table('nobs_transactions')->where('comp_id', $this->compId);
        
        $totalDeposits = (float)(clone $baseQuery)->where('name_of_transaction', 'Deposit')->sum('amount');
        $totalWithdrawals = (float)(clone $baseQuery)->where('name_of_transaction', 'Withdraw')->sum('amount');
        $totalRepayments = (float)(clone $baseQuery)->where('name_of_transaction', 'Loan Repayment')->sum('amount');
        $totalSystemCommissions = (float)(clone $baseQuery)->where('name_of_transaction', 'Commission')->sum('amount');
        $totalAgentCommissions = (float)(clone $baseQuery)->where('name_of_transaction', 'Agent Commission')->sum('amount');

        // Cash-at-Hand Formula: (All Money In) - (All Money Out)
        $cashAtHand = ($totalDeposits + $totalRepayments) - ($totalWithdrawals + $totalSystemCommissions + $totalAgentCommissions);

        // 2. MONTHLY PERFORMANCE (Current Month Velocity)
        $monthlyDeposits = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Deposit')->where('created_at', '>=', $monthStart)->sum('amount');
        $monthlyWithdrawals = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Withdraw')->where('created_at', '>=', $monthStart)->sum('amount');
        $monthlyRepayments = DB::table('nobs_transactions')->where('comp_id', $this->compId)->where('name_of_transaction', 'Loan Repayment')->where('created_at', '>=', $monthStart)->sum('amount');
        
        // 3. LOAN DISBURSEMENT DATA
        $monthlyDisbursed = DB::table('loan_applications')
            ->where('comp_id', $this->compId)
            ->whereIn('status', ['active', 'disbursed', 'repaid'])
            ->where('updated_at', '>=', $monthStart)
            ->sum('amount');

        // 4. RISK EXPOSURE
        $arrears = DB::table('loan_repayment_schedules')
            ->where('comp_id', $this->compId)
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'paid')
            ->select(
                DB::raw("COUNT(*) as count"),
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_amount")
            )->first();

        $dormantCount = DB::table('nobs_user_account_numbers')->where('comp_id', $this->compId)->where('account_status', 'dormant')->count();

        // COMPREHENSIVE DATA PACKAGE FOR AI BRAIN
        $fullSystemData = [
            'state_of_money' => [
                'true_cash_at_hand' => $cashAtHand,
                'monthly_deposits' => $monthlyDeposits,
                'monthly_withdrawals' => $monthlyWithdrawals,
                'monthly_repayments' => $monthlyRepayments,
                'monthly_loan_disbursements' => $monthlyDisbursed
            ],
            'risk_exposure' => [
                'unpaid_loans_total' => $arrears->total_amount,
                'unpaid_cases' => $arrears->count,
                'dormant_accounts' => $dormantCount
            ]
        ];

        $prompt = "Act as a CFO. Analyze this bank performance data for the current month: " . json_encode($fullSystemData) . ". 
        Compare Monthly Deposits vs Withdrawals. Assess if repayments (GHS " . number_format($monthlyRepayments, 2) . ") are keeping pace with disbursements (GHS " . number_format($monthlyDisbursed, 2) . ").
        MANDATORY: Return a JSON object with one key 'strategy' containing a 3-sentence high-level summary and strategic advice. No markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? ($brief['content'] ?? 'Strategic review based on monthly cash flow and portfolio risk.');

        // Build metadata - CORE FINANCIAL STATE (Core metrics always show, others filtered)
        $metadata = [
            ['Cash at Hand' => number_format($cashAtHand, 2) . ' GHS'],
            ['Monthly Deposits' => number_format($monthlyDeposits, 2) . ' GHS'],
            ['Monthly Withdrawals' => number_format($monthlyWithdrawals, 2) . ' GHS'],
            ['Monthly Repayments' => number_format($monthlyRepayments, 2) . ' GHS'],
            ['MTD Disbursements' => number_format($monthlyDisbursed, 2) . ' GHS'],
            ['Unpaid Loans Balance' => number_format($arrears->total_amount, 2) . ' GHS'],
            ['Dormant Accounts' => (int)$dormantCount],
            ['Strategic Review' => $strategyText]
        ];

        // Filter out zero/empty rows for clean UI
        $filteredMetadata = array_values(array_filter($metadata, function($item) {
            $val = array_values($item)[0];
            return !($val === '0.00 GHS' || $val === 0 || $val === '0' || empty($val));
        }));

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $filteredMetadata,
            'caption' => 'Strategic Executive Intelligence:'
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
