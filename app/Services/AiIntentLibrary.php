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
     * Exact Replica: Daily Transaction Summary
     */
    public function getDailySummary($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $metrics = DB::table('nobs_transactions')
            ->select(
                DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS deposits'),
                DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS withdrawals'),
                DB::raw('SUM(CASE WHEN name_of_transaction = "Loan Repayment" THEN amount ELSE 0 END) AS repayments'),
                DB::raw('COUNT(CASE WHEN name_of_transaction = "Deposit" THEN 1 END) as deposit_count'),
                DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdraw" THEN 1 END) as withdrawal_count')
            )
            ->whereDate('created_at', $date)
            ->where('comp_id', $this->compId)
            ->where('is_shown', 1)
            ->first();

        $registered = DB::table('nobs_registration')
            ->where('comp_id', $this->compId)
            ->whereDate('created_at', $date)
            ->count();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => [
                ['Metric' => 'Deposits', 'Value' => number_format($metrics->deposits ?? 0, 2), 'Count' => $metrics->deposit_count ?? 0],
                ['Metric' => 'Withdrawals', 'Value' => number_format($metrics->withdrawals ?? 0, 2), 'Count' => $metrics->withdrawal_count ?? 0],
                ['Metric' => 'Repayments', 'Value' => number_format($metrics->repayments ?? 0, 2)],
                ['Metric' => 'New Customers', 'Value' => $registered]
            ],
            'caption' => "Daily Activity Summary ($date):"
        ];
    }

    /**
     * Exact Replica: Recent Transactions
     */
    public function getRecentTransactions($limit = 5)
    {
        $tx = DB::table('nobs_transactions')
            ->select('created_at', 'name_of_transaction as type', 'amount', 'det_rep_name_of_transaction as customer')
            ->where('comp_id', $this->compId)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $tx,
            'caption' => "Last $limit transactions:"
        ];
    }

    /**
     * Exact Replica: New Registrations
     */
    public function getRecentRegistrations($limit = 5)
    {
        $regs = DB::table('nobs_registration')
            ->select('created_at', 'first_name', 'surname', 'account_number', 'phone_number')
            ->where('comp_id', $this->compId)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $regs,
            'caption' => "Recent registrations:"
        ];
    }

    /**
     * Exact Replica: Customers due for repayment today
     */
    public function getExpectedRepayments($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $list = DB::table('loan_repayment_schedules')
            ->join('loan_applications', 'loan_repayment_schedules.loan_application_id', '=', 'loan_applications.id')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select(
                'nobs_registration.first_name',
                'nobs_registration.surname',
                'nobs_registration.phone_number',
                DB::raw("(principal_due + interest_due + fees_due) - (principal_paid + interest_paid + fees_paid) as balance_due")
            )
            ->where('loan_repayment_schedules.comp_id', $this->compId)
            ->whereDate('loan_repayment_schedules.due_date', $date)
            ->where('loan_repayment_schedules.status', '!=', 'paid')
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $list,
            'caption' => "Collections Due Today ($date):"
        ];
    }

    /**
     * Exact Replica: Loan Disbursement Today
     */
    public function getDailyDisbursements($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $loans = DB::table('loan_applications')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select('nobs_registration.first_name', 'nobs_registration.surname', 'amount', 'loan_applications.updated_at as time')
            ->where('loan_applications.comp_id', $this->compId)
            ->whereIn('loan_applications.status', ['active', 'disbursed'])
            ->whereDate('loan_applications.updated_at', $date)
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $loans,
            'caption' => "Disbursements Today ($date):"
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
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $pending,
            'caption' => "Loans Awaiting Approval:"
        ];
    }

    /**
     * Exact Replica: Collection Mobilization by Agent
     */
    public function getAgentCollections($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $collections = DB::table('nobs_transactions')
            ->select('agentname', DB::raw('SUM(amount) as total_collected'), DB::raw('COUNT(*) as count'))
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', 'Deposit')
            ->whereDate('created_at', $date)
            ->groupBy('users', 'agentname')
            ->orderBy('total_collected', 'DESC')
            ->get();

        return [
            'ui_type' => 'mobile_optimized_list',
            'ui_metadata' => $collections,
            'caption' => "Agent Collections ($date):"
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
            'caption' => "Agent Rankings:"
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

    public function getFinancialSummary($type = 'Deposit', $date = null)
    {
        $date = $date ?: date('Y-m-d');
        $transType = ($type === 'Withdraw') ? 'Withdraw' : 'Deposit';

        $total = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', $transType)
            ->whereDate('created_at', $date)
            ->where('amount', '<', 1000000)
            ->where('name_of_transaction', 'NOT LIKE', '%reversal%')
            ->sum('amount');

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Daily $transType",
                'value' => number_format($total, 2),
                'suffix' => 'GHS'
            ],
            'caption' => "The total $transType amount for today is GHS " . number_format($total, 2)
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
                ['label' => '💰 Deposits Today', 'query' => 'Show total deposits'],
                ['label' => '💸 Withdrawals Today', 'query' => 'Show total withdrawals'],
                ['label' => '🧾 Recent Tx', 'query' => 'Show last 5 transactions'],
                ['label' => '⬅️ Back', 'query' => 'help']
            ];
            $caption = "Daily Transaction Intelligence:";
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
