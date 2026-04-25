<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * AiIntentLibrary - The "Verified Truth" Layer.
 * Every function here mirrors the EXACT code in the SABS Dashboards/Reports.
 */
class AiIntentLibrary
{
    private $compId;

    public function __construct($compId = null)
    {
        $this->compId = $compId ?: Auth::user()->comp_id;
    }

    /**
     * Helper: Normalizes single date or range into [start, end]
     */
    private function applyDateRange($query, $startDate = null, $endDate = null, $column = 'created_at')
    {
        $start = $startDate ?: date('Y-m-d');
        $end = $endDate ?: $start;

        if ($start === $end) {
            return $query->whereDate($column, $start);
        }
        return $query->whereBetween($column, [$start . ' 00:00:00', $end . ' 23:59:59']);
    }

    private function getLabel($startDate, $endDate)
    {
        $start = $startDate ?: date('Y-m-d');
        $end = $endDate ?: $start;
        return ($start === $end) ? "on $start" : "from $start to $end";
    }

    /**
     * Exact Replica: Net System Position (from ReportSystemController)
     */
    public function getSystemLiquidity()
    {
        $baseQuery = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('amount', '<', 1000000)
            ->where('name_of_transaction', 'NOT LIKE', '%reversal%')
            ->where('description', 'NOT LIKE', '%reversal%');

        $totalDeposits = (float)(clone $baseQuery)->where('name_of_transaction', 'Deposit')->sum('amount');
        $totalWithdrawals = (float)(clone $baseQuery)->where('name_of_transaction', 'Withdraw')->sum('amount');
        $totalRepayments = (float)(clone $baseQuery)->where('name_of_transaction', 'Loan Repayment')->sum('amount');
        
        $totalFees = (float)(clone $baseQuery)->where(function($q) {
            $q->where('name_of_transaction', 'LIKE', '%fee%')
              ->orWhere('name_of_transaction', 'LIKE', '%charge%')
              ->orWhere('name_of_transaction', 'sms')
              ->orWhere('name_of_transaction', 'maintenance');
        })->sum('amount');

        $cashInHand = ($totalDeposits + $totalRepayments) - $totalWithdrawals;
        $netPosition = $cashInHand - ($totalDeposits - ($totalWithdrawals + $totalFees));

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Net System Position",
                'value' => number_format($netPosition, 2),
                'suffix' => 'GHS',
                'details' => "Cash in Hand: " . number_format($cashInHand, 2)
            ],
            'caption' => "The net position is GHS " . number_format($netPosition, 2) . ". Cash in hand: GHS " . number_format($cashInHand, 2)
        ];
    }

    /**
     * Exact Replica: Company Cash & Pool Balances
     */
    public function getCashAndPool()
    {
        $companyCash = DB::table('company_info')->where('id', $this->compId)->value('amount_in_cash') ?? 0;
        $poolBalance = DB::table('nobs_loans_central_account')->where('comp_id', $this->compId)->sum('balance') ?? 0;

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => [
                ['Account' => 'Company Cash', 'Amount' => number_format($companyCash, 2)],
                ['Account' => 'Loan Pool', 'Amount' => number_format($poolBalance, 2)]
            ],
            'caption' => "Current Cash & Pool Balances:"
        ];
    }

    /**
     * Exact Replica: Transaction Summary (supports range)
     */
    public function getDailySummary($startDate = null, $endDate = null)
    {
        $metricsQuery = DB::table('nobs_transactions')
            ->select(
                DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS deposits'),
                DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS withdrawals'),
                DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS repayments'),
                DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as deposit_count'),
                DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as withdrawal_count')
            )
            ->where('comp_id', $this->compId)
            ->where('is_shown', 1);
        
        $metrics = $this->applyDateRange($metricsQuery, $startDate, $endDate)->first();

        $registeredQuery = DB::table('nobs_registration')->where('comp_id', $this->compId);
        $registered = $this->applyDateRange($registeredQuery, $startDate, $endDate)->count();

        $label = $this->getLabel($startDate, $endDate);

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => [
                ['Metric' => 'Deposits', 'Value' => number_format($metrics->deposits ?? 0, 2), 'Count' => $metrics->deposit_count ?? 0],
                ['Metric' => 'Withdrawals', 'Value' => number_format($metrics->withdrawals ?? 0, 2), 'Count' => $metrics->withdrawal_count ?? 0],
                ['Metric' => 'Repayments', 'Value' => number_format($metrics->repayments ?? 0, 2)],
                ['Metric' => 'New Customers', 'Value' => $registered]
            ],
            'caption' => "Activity Summary $label:"
        ];
    }

    /**
     * Recent Transactions (filtered by date if provided)
     */
    public function getRecentTransactions($limit = 5, $startDate = null, $endDate = null)
    {
        $query = DB::table('nobs_transactions')
            ->select('created_at', 'name_of_transaction as type', 'amount', 'det_rep_name_of_transaction as customer')
            ->where('comp_id', $this->compId);

        if ($startDate) {
            $query = $this->applyDateRange($query, $startDate, $endDate);
        }

        $tx = $query->orderBy('id', 'DESC')->limit($limit)->get();
        $label = $startDate ? $this->getLabel($startDate, $endDate) : "most recent";

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $tx,
            'caption' => "Transactions ($label):"
        ];
    }

    /**
     * New Registrations (supports range)
     */
    public function getRecentRegistrations($limit = 10, $startDate = null, $endDate = null)
    {
        $query = DB::table('nobs_registration')
            ->select('created_at', 'first_name', 'surname', 'account_number', 'phone_number')
            ->where('comp_id', $this->compId);

        if ($startDate) {
            $query = $this->applyDateRange($query, $startDate, $endDate);
        }

        $regs = $query->orderBy('id', 'DESC')->limit($limit)->get();
        $label = $startDate ? $this->getLabel($startDate, $endDate) : "most recent";

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $regs,
            'caption' => "Registrations ($label):"
        ];
    }

    /**
     * Repayments Due (supports range)
     */
    public function getExpectedRepayments($startDate = null, $endDate = null)
    {
        $query = DB::table('loan_repayment_schedules')
            ->join('loan_applications', 'loan_repayment_schedules.loan_application_id', '=', 'loan_applications.id')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select(
                'nobs_registration.first_name',
                'nobs_registration.surname',
                'loan_repayment_schedules.due_date',
                DB::raw("(principal_due + interest_due + fees_due) - (principal_paid + interest_paid + fees_paid) as balance_due")
            )
            ->where('loan_repayment_schedules.comp_id', $this->compId)
            ->where('loan_repayment_schedules.status', '!=', 'paid');
        
        $list = $this->applyDateRange($query, $startDate, $endDate, 'loan_repayment_schedules.due_date')
            ->limit(20) // SCALE PROTECTION
            ->get();
        $label = $this->getLabel($startDate, $endDate);

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $list,
            'caption' => "Expected Repayments $label:"
        ];
    }

    /**
     * Loan Disbursements (supports range)
     */
    public function getDailyDisbursements($startDate = null, $endDate = null)
    {
        $query = DB::table('loan_applications')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select('nobs_registration.first_name', 'nobs_registration.surname', 'amount', 'loan_applications.updated_at as time')
            ->where('loan_applications.comp_id', $this->compId)
            ->whereIn('loan_applications.status', ['active', 'disbursed']);
        
        $loans = $this->applyDateRange($query, $startDate, $endDate, 'loan_applications.updated_at')
            ->limit(20) // SCALE PROTECTION
            ->get();
        $label = $this->getLabel($startDate, $endDate);

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $loans,
            'caption' => "Disbursements $label:"
        ];
    }

    /**
     * Exact Replica: Pending Loan Requests
     */
    public function getPendingLoans()
    {
        $pending = DB::table('loan_applications')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select('nobs_registration.first_name', 'nobs_registration.surname', 'amount', 'loan_applications.status')
            ->where('loan_applications.comp_id', $this->compId)
            ->whereIn('loan_applications.status', ['pending', 'pending_approval'])
            ->limit(20) // SCALE PROTECTION
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $pending,
            'caption' => "Loans Awaiting Approval:"
        ];
    }

    /**
     * Collection Mobilization (supports range)
     */
    public function getAgentCollections($startDate = null, $endDate = null)
    {
        $query = DB::table('nobs_transactions')
            ->select('agentname', DB::raw('SUM(amount) as total_collected'), DB::raw('COUNT(*) as count'))
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', 'Deposit')
            ->groupBy('users', 'agentname');
        
        $collections = $this->applyDateRange($query, $startDate, $endDate)
            ->orderBy('total_collected', 'DESC')
            ->limit(20) // SCALE PROTECTION
            ->get();
        $label = $this->getLabel($startDate, $endDate);

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $collections,
            'caption' => "Agent Collections $label:"
        ];
    }

    /**
     * Exact Replica: Grouped Arrears Report
     */
    public function getArrearsList()
    {
        $arrears = DB::table('loan_repayment_schedules')
            ->join('loan_applications', 'loan_repayment_schedules.loan_application_id', '=', 'loan_applications.id')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select(
                'nobs_registration.first_name',
                'nobs_registration.surname',
                DB::raw("SUM(principal_due + interest_due + fees_due - (principal_paid + interest_paid + fees_paid)) as total_arrears"),
                DB::raw("COUNT(*) as missed_installments")
            )
            ->where('loan_repayment_schedules.comp_id', $this->compId)
            ->where('loan_repayment_schedules.due_date', '<', date('Y-m-d'))
            ->where('loan_repayment_schedules.status', '!=', 'paid')
            ->groupBy('nobs_registration.id', 'nobs_registration.first_name', 'nobs_registration.surname')
            ->having('total_arrears', '>', 0)
            ->orderBy('total_arrears', 'DESC')
            ->limit(10)
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $arrears,
            'caption' => "Arrears Report:"
        ];
    }

    /**
     * Exact Replica: Agent Performance
     */
    public function getAgentPerformance($month = null)
    {
        $month = $month ?: date('m');
        $performance = DB::table('agent_commissions')
            ->join('users', 'agent_commissions.agent_id', '=', 'users.id')
            ->select(
                'users.name as agent_name',
                DB::raw("SUM(amount) as total_commissions"),
                DB::raw("COUNT(*) as transaction_count")
            )
            ->where('agent_commissions.comp_id', $this->compId)
            ->whereMonth('agent_commissions.created_at', $month)
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_commissions', 'DESC')
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $performance,
            'caption' => "Agent Rankings (Month $month):"
        ];
    }

    /**
     * Exact Replica: Portfolio Summary
     */
    public function getPortfolioSummary()
    {
        $summary = DB::table('loan_applications')
            ->select(
                'status',
                DB::raw("COUNT(*) as total_loans"),
                DB::raw("SUM(amount) as total_principal")
            )
            ->where('comp_id', $this->compId)
            ->groupBy('status')
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $summary,
            'caption' => "Portfolio Summary:"
        ];
    }

    public function searchCustomers($term)
    {
        $term = trim($term);
        if (empty($term)) return ['ui_type' => 'text', 'ui_metadata' => [], 'caption' => 'Please provide a name or account number.'];

        $query = DB::table('nobs_registration')
            ->select('id', 'first_name', 'surname', 'account_number', 'phone_number')
            ->where('comp_id', $this->compId);

        $parts = explode(' ', $term);
        if (count($parts) > 1) {
            $query->where(function($q) use ($parts, $term) {
                // All parts must match either first_name or surname
                $q->where(function($sq) use ($parts) {
                    foreach ($parts as $p) {
                        $sq->where(function($ssq) use ($p) {
                            $ssq->where('first_name', 'LIKE', "%$p%")
                                ->orWhere('surname', 'LIKE', "%$p%");
                        });
                    }
                })
                ->orWhere('account_number', 'LIKE', "%$term%");
            });
        } else {
            $query->where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%$term%")
                  ->orWhere('surname', 'LIKE', "%$term%")
                  ->orWhere('account_number', 'LIKE', "%$term%");
            });
        }

        $customers = $query->limit(5)->get();

        if ($customers->isEmpty()) {
            return ['ui_type' => 'text', 'ui_metadata' => [], 'caption' => "I could not find any customer matching '$term'."];
        }

        return [
            'ui_type' => 'customer_card',
            'ui_metadata' => $customers,
            'caption' => "I found " . count($customers) . " matching customer(s):"
        ];
    }

    public function getFinancialSummary($type = 'Deposit', $startDate = null, $endDate = null)
    {
        $transType = ($type === 'Withdraw') ? 'Withdraw' : 'Deposit';

        $query = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', $transType)
            ->where('amount', '<', 1000000)
            ->where('name_of_transaction', 'NOT LIKE', '%reversal%');
        
        $total = $this->applyDateRange($query, $startDate, $endDate)->sum('amount');
        $label = $this->getLabel($startDate, $endDate);

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Total $transType",
                'value' => number_format($total, 2),
                'suffix' => 'GHS'
            ],
            'caption' => "The total $transType amount $label is GHS " . number_format($total, 2)
        ];
    }

    /**
     * Exact Replica: Account Balances by Type (from ApiUsersController)
     */
    public function getAccountBalancesByType()
    {
        $balances = DB::table('nobs_user_account_numbers')
            ->select(
                'account_type',
                DB::raw('SUM(balance) as total_balance'),
                DB::raw('COUNT(id) as account_count')
            )
            ->where('comp_id', $this->compId)
            ->groupBy('account_type')
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $balances,
            'caption' => "Balance Breakdown:"
        ];
    }

    public function getHelpMenu($role = 'Staff', $menuType = 'main')
    {
        $role = strtolower($role);
        $isAdmin = in_array($role, ['admin', 'owner', 'super admin', 'manager']);

        if ($menuType === 'liquidity') {
            $capabilities = [
                ['label' => '🏦 Net Position', 'query' => 'Check system liquidity'],
                ['label' => '🏧 Account Balances', 'query' => 'Show account balance breakdown'],
                ['label' => '🏊 Cash & Pool', 'query' => 'Show company cash and pool balances'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Liquidity & Banking Intelligence:";
        } elseif ($menuType === 'transactions') {
            $capabilities = [
                ['label' => '📊 Daily Summary', 'query' => 'Today summary'],
                ['label' => '💰 Deposits...', 'query' => 'menu deposits'],
                ['label' => '💸 Withdrawals...', 'query' => 'menu withdrawals'],
                ['label' => '🧾 Recent 5', 'query' => 'Show last 5 transactions'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Transaction Intelligence:";
        } elseif ($menuType === 'deposits') {
            $capabilities = [
                ['label' => '💰 Today', 'query' => 'Total deposits today'],
                ['label' => '📅 Yesterday', 'query' => 'Total deposits yesterday'],
                ['label' => '🗓️ This Week', 'query' => 'Total deposits this week'],
                ['label' => '📊 This Month', 'query' => 'Total deposits this month'],
                ['label' => '⬅️ Back', 'query' => 'menu transactions']
            ];
            $caption = "Deposit Analytics:";
        } elseif ($menuType === 'withdrawals') {
            $capabilities = [
                ['label' => '💸 Today', 'query' => 'Total withdrawals today'],
                ['label' => '📅 Yesterday', 'query' => 'Total withdrawals yesterday'],
                ['label' => '🗓️ This Week', 'query' => 'Total withdrawals this week'],
                ['label' => '📊 This Month', 'query' => 'Total withdrawals this month'],
                ['label' => '⬅️ Back', 'query' => 'menu transactions']
            ];
            $caption = "Withdrawal Analytics:";
        } elseif ($menuType === 'customers') {
            $capabilities = [
                ['label' => '🔍 Find Customer', 'query' => 'Search for customer'],
                ['label' => '🆕 New Today', 'query' => 'New registrations today'],
                ['label' => '👥 Recent 5', 'query' => 'Show last 5 customers'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Customer & CRM Intelligence:";
        } elseif ($menuType === 'loans' && $isAdmin) {
            $capabilities = [
                ['label' => '📈 Portfolio Health', 'query' => 'Loan portfolio summary'],
                ['label' => '💸 Arrears List', 'query' => 'Who is in arrears?'],
                ['label' => '📅 Expected Today', 'query' => 'Repayments due today'],
                ['label' => '⏳ Pending Loans', 'query' => 'Loans awaiting approval'],
                ['label' => '🏗️ Disbursed Today', 'query' => 'Loans paid today'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Loan Portfolio Intelligence:";
        } elseif ($menuType === 'performance' && $isAdmin) {
            $capabilities = [
                ['label' => '🏆 Agent Ranking', 'query' => 'Top performing agents'],
                ['label' => '🤝 Agent Collections', 'query' => 'Today agent collections'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Growth & Performance Intelligence:";
        } else {
            // Main Menu
            $capabilities = [
                ['label' => '🏦 Liquidity', 'query' => 'menu liquidity'],
                ['label' => '💰 Transactions', 'query' => 'menu transactions'],
                ['label' => '👥 Customers', 'query' => 'menu customers'],
            ];

            if ($isAdmin) {
                $capabilities[] = ['label' => '💸 Loans', 'query' => 'menu loans'];
                $capabilities[] = ['label' => '📈 Performance', 'query' => 'menu performance'];
            }
            
            $caption = "I am your SABS Intelligence Assistant. How can I help you today?";
        }

        return ['ui_type' => 'capability_chips', 'ui_metadata' => $capabilities, 'caption' => $caption];
    }
}
