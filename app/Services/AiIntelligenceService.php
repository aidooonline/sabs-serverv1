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
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $monthStart = date('Y-m-01');

        // 1. LIQUIDITY & BANKING VELOCITY
        $liquidity = $this->intentLibrary->getSystemLiquidity();
        $cashInHand = $liquidity['ui_metadata']['details'] ?? '0.00 GHS';
        
        // 2. TRANSACTION VELOCITY (Enriched for AI reasoning)
        $txStats = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->select(
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Deposit' AND DATE(created_at) = '$today' THEN amount ELSE 0 END) as dep_today"),
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Deposit' AND DATE(created_at) = '$yesterday' THEN amount ELSE 0 END) as dep_yesterday"),
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Deposit' AND created_at >= '$monthStart' THEN amount ELSE 0 END) as dep_month"),
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Withdraw' AND DATE(created_at) = '$today' THEN amount ELSE 0 END) as with_today"),
                DB::raw("SUM(CASE WHEN name_of_transaction = 'Loan Repayment' AND DATE(created_at) = '$today' THEN amount ELSE 0 END) as rep_today")
            )->first();

        // 3. LOAN & ARREARS RISK
        $arrears = DB::table('loan_repayment_schedules')
            ->where('comp_id', $this->compId)
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'paid')
            ->select(
                DB::raw("COUNT(*) as count"),
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_amount")
            )->first();

        // 4. GROWTH & STATUS
        $dormantCount = DB::table('nobs_user_account_numbers')->where('comp_id', $this->compId)->where('account_status', 'dormant')->count();
        $newRegToday = DB::table('nobs_registration')->where('comp_id', $this->compId)->whereDate('created_at', $today)->count();
        $company = DB::table('accounts')->where('id', $this->compId)->first();
        $lastRun = $company->loan_cron_last_run ? Carbon::parse($company->loan_cron_last_run)->diffForHumans() : 'Never';

        // COMPREHENSIVE DATA PACKAGE FOR AI BRAIN
        $fullSystemData = [
            'liquidity_state' => ['net_position' => $liquidity['ui_metadata']['value'], 'cash_on_hand' => $cashInHand],
            'transaction_performance' => [
                'deposits_today' => $txStats->dep_today,
                'deposits_yesterday' => $txStats->dep_yesterday,
                'deposits_this_month' => $txStats->dep_month,
                'withdrawals_today' => $txStats->with_today,
                'loan_repayments_today' => $txStats->rep_today
            ],
            'risk_exposure' => ['unpaid_loans_total' => $arrears->total_amount, 'unpaid_cases' => $arrears->count],
            'system_health' => ['dormant_accounts' => $dormantCount, 'new_registrations_today' => $newRegToday, 'last_process_run' => $lastRun]
        ];

        $prompt = "Act as a highly experienced CFO and Product Strategist. Analyze this complete bank data package: " . json_encode($fullSystemData) . ". 
        1. Compare Today's Deposits (GHS " . number_format($txStats->dep_today, 2) . ") vs Yesterday (GHS " . number_format($txStats->dep_yesterday, 2) . ").
        2. Comment on the Arrears Risk vs Net Liquidity.
        3. MANDATORY: Return a JSON object with one key 'strategy'. 
        The value must be a professional 3-sentence executive review and advice for the CEO. Be specific with numbers. Do not use markdown.";

        $brief = $this->callGeminiBasic($prompt);
        $strategyText = $brief['strategy'] ?? ($brief['content'] ?? 'Strategic review pending data synchronization.');

        // 5. OUTPUT CONSTRUCTION (2-Decimal Precision)
        $rawMetadata = [
            ['Net Liquidity' => number_format((float)str_replace(',', '', $liquidity['ui_metadata']['value'] ?? 0), 2) . ' GHS'],
            ['Deposits Today' => number_format($txStats->dep_today, 2) . ' GHS'],
            ['Deposits Yesterday' => number_format($txStats->dep_yesterday, 2) . ' GHS'],
            ['Deposits Month' => number_format($txStats->dep_month, 2) . ' GHS'],
            ['Withdrawals Today' => number_format($txStats->with_today, 2) . ' GHS'],
            ['Unpaid Loans' => number_format($arrears->total_amount, 2) . ' GHS'],
            ['Dormant Accounts' => (int)$dormantCount],
            ['New Reg Today' => (int)$newRegToday],
            ['System Processed' => $lastRun],
            ['Strategic Review' => $strategyText]
        ];

        // STRICT FILTERING: Remove any row where the value is zero, 0.00, or empty
        $filteredMetadata = array_values(array_filter($rawMetadata, function($item) {
            $val = array_values($item)[0];
            if ($val === '0.00 GHS' || $val === 0 || $val === '0' || empty($val)) return false;
            return true;
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
