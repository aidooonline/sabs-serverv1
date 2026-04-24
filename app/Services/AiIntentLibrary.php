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
            'caption' => "The net system position is GHS " . number_format($netPosition, 2) . ". Total cash on hand is GHS " . number_format($cashInHand, 2) . "."
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

    public function getHelpMenu($role = 'Staff')
    {
        $role = strtolower($role);
        $capabilities = [
            ['label' => '🏦 Bank Liquidity', 'query' => 'Check system liquidity'],
            ['label' => '💰 Deposits Today', 'query' => 'Show total deposits'],
            ['label' => '👥 Find Customer', 'query' => 'Search for customer'],
        ];

        if (in_array($role, ['admin', 'owner', 'super admin', 'manager'])) {
            $capabilities[] = ['label' => '💸 Arrears Report', 'query' => 'Who is in arrears?'];
            $capabilities[] = ['label' => '🏆 Agent Ranking', 'query' => 'Top performing agents'];
            $capabilities[] = ['label' => '📈 Portfolio Health', 'query' => 'Loan portfolio summary'];
            $caption = "Executive Analyst Online. Select a verified business tool:";
        } else {
            $capabilities[] = ['label' => '🔍 Loan Status', 'query' => 'Recent loan status'];
            $caption = "Hello! I am your SABS Assistant. How can I help you today?";
        }

        return ['ui_type' => 'capability_chips', 'ui_metadata' => $capabilities, 'caption' => $caption];
    }
}
