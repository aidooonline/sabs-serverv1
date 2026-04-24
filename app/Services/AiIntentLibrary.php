<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * AiIntentLibrary - Mirroring Production Business Logic for 100% Accuracy.
 */
class AiIntentLibrary
{
    private $compId;

    public function __construct($compId = null)
    {
        $this->compId = $compId ?: Auth::user()->comp_id;
    }

    /**
     * Exact replica of ReportSystemController's Live Report Logic
     */
    public function getAccountSummary($date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $baseQuery = DB::table('nobs_transactions')
            ->where('comp_id', $this->compId)
            ->whereDate('created_at', '<=', $date)
            ->where('amount', '<', 1000000)
            ->where('name_of_transaction', 'NOT LIKE', '%reversal%')
            ->where('description', 'NOT LIKE', '%reversal%');

        $deposits = (float)(clone $baseQuery)->where('name_of_transaction', 'Deposit')->sum('amount');
        $withdrawals = (float)(clone $baseQuery)->where('name_of_transaction', 'Withdraw')->sum('amount');
        $repayments = (float)(clone $baseQuery)->where('name_of_transaction', 'Loan Repayment')->sum('amount');
        
        $cashInHand = ($deposits + $repayments) - $withdrawals;

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Bank Liquidity (as of $date)",
                'value' => number_format($cashInHand, 2),
                'suffix' => 'GHS',
                'details' => "Deposits: " . number_format($deposits, 2) . " | Repayments: " . number_format($repayments, 2)
            ],
            'caption' => "As of $date, the total cash in hand is GHS " . number_format($cashInHand, 2)
        ];
    }

    /**
     * High-Accuracy Total Deposits/Withdrawals
     */
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
            ->where('description', 'NOT LIKE', '%reversal%')
            ->sum('amount');

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Total $transType" . ($date === date('Y-m-d') ? " (Today)" : " ($date)"),
                'value' => number_format($total, 2),
                'suffix' => 'GHS'
            ],
            'caption' => "The total $transType amount for $date is GHS " . number_format($total, 2)
        ];
    }

    /**
     * Enhanced Customer Search matching ApiUsersController
     */
    public function searchCustomers($term)
    {
        $customers = DB::table('nobs_registration')
            ->leftJoin('nobs_user_account_numbers', 'nobs_registration.account_number', '=', 'nobs_user_account_numbers.account_number')
            ->select('nobs_registration.id', 'nobs_registration.first_name', 'nobs_registration.surname', 'nobs_registration.account_number', 'nobs_registration.phone_number', 'nobs_user_account_numbers.account_status')
            ->where('nobs_registration.comp_id', $this->compId)
            ->where(function($q) use ($term) {
                $q->where('nobs_registration.first_name', 'LIKE', "%$term%")
                  ->orWhere('nobs_registration.surname', 'LIKE', "%$term%")
                  ->orWhere('nobs_registration.account_number', 'LIKE', "%$term%")
                  ->orWhere('nobs_registration.phone_number', 'LIKE', "%$term%");
            })
            ->limit(5)
            ->get();

        return [
            'ui_type' => 'customer_card',
            'ui_metadata' => $customers,
            'caption' => "I found " . count($customers) . " customers matching '$term'."
        ];
    }

    /**
     * Portfolio Analysis matching ReportSystemController
     */
    public function getLoanOverview()
    {
        $stats = DB::table('loan_applications')
            ->select(
                'status', 
                DB::raw('count(*) as count'), 
                DB::raw('SUM(amount) as total_disbursed'),
                DB::raw("(SELECT COALESCE(SUM(principal_paid + interest_paid + fees_paid), 0) FROM loan_repayment_schedules WHERE loan_repayment_schedules.comp_id = loan_applications.comp_id) as total_recovered")
            )
            ->where('comp_id', $this->compId)
            ->groupBy('status')
            ->get();

        return [
            'ui_type' => 'data_table',
            'ui_metadata' => $stats,
            'caption' => "Here is the verified loan portfolio status."
        ];
    }

    public function getHelpMenu($role = 'Staff')
    {
        $role = strtolower($role);
        $capabilities = [
            ['label' => '📈 Liquidity', 'query' => 'What is the bank liquidity?'],
            ['label' => '💰 Deposits Today', 'query' => 'Total deposits today'],
            ['label' => '👥 Find Customer', 'query' => 'Search for a customer'],
        ];

        if (in_array($role, ['admin', 'owner', 'super admin', 'manager'])) {
            $capabilities[] = ['label' => '💸 Who is in Arrears?', 'query' => 'Show me customers in arrears'];
            $capabilities[] = ['label' => '🏆 Top Agents', 'query' => 'Who are the top agents this month?'];
            $caption = "Executive Access Granted. I am ready to analyze your bank's performance:";
        } else {
            $capabilities[] = ['label' => '🔍 Loan Status', 'query' => 'Check status of recent loans'];
            $caption = "Hello! I'm your SABS field assistant. How can I help you manage your records today?";
        }

        return [
            'ui_type' => 'capability_chips',
            'ui_metadata' => $capabilities,
            'caption' => $caption
        ];
    }
}
