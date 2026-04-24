<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * AiIntentLibrary - Precision Business Logic.
 */
class AiIntentLibrary
{
    private $compId;

    public function __construct($compId = null)
    {
        $this->compId = $compId ?: Auth::user()->comp_id;
    }

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
                'title' => "Bank Liquidity",
                'value' => number_format($cashInHand, 2),
                'suffix' => 'GHS',
                'details' => "Deposits: " . number_format($deposits, 2) . " | Repayments: " . number_format($repayments, 2)
            ],
            'caption' => "The net liquidity as of $date is GHS " . number_format($cashInHand, 2)
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
                'title' => "Total $transType (Today)",
                'value' => number_format($total, 2),
                'suffix' => 'GHS'
            ],
            'caption' => "Total $transType for today is GHS " . number_format($total, 2)
        ];
    }

    public function searchCustomers($term)
    {
        $term = trim($term);
        if (empty($term)) return ['ui_type' => 'text', 'ui_metadata' => [], 'caption' => 'Please provide a search term.'];

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
            ->limit(10)
            ->get();

        if ($customers->isEmpty()) {
            return ['ui_type' => 'text', 'ui_metadata' => [], 'caption' => "I could not find any customer matching '$term'."];
        }

        return [
            'ui_type' => 'customer_card',
            'ui_metadata' => $customers,
            'caption' => "I found " . count($customers) . " records for '$term'."
        ];
    }

    public function getLoanOverview()
    {
        // Grouped by customer for the AI to show clean lists
        $stats = DB::table('loan_applications')
            ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
            ->select(
                'nobs_registration.first_name', 
                'nobs_registration.surname',
                'loan_applications.status', 
                'loan_applications.amount as principal',
                DB::raw("(SELECT COALESCE(SUM(principal_paid + interest_paid + fees_paid), 0) FROM loan_repayment_schedules WHERE loan_application_id = loan_applications.id) as total_paid")
            )
            ->where('loan_applications.comp_id', $this->compId)
            ->whereIn('loan_applications.status', ['active', 'defaulted'])
            ->limit(10)
            ->get();

        return [
            'ui_type' => 'data_table',
            'ui_metadata' => $stats,
            'caption' => "Here are your currently active loans and their repayment status."
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
            $capabilities[] = ['label' => '💸 Who is in Arrears?', 'query' => 'Who is in arrears?'];
            $capabilities[] = ['label' => '🏆 Top Agents', 'query' => 'Top agents this month'];
            $caption = "Welcome Stephen. How can I help you manage SABS Bank today?";
        } else {
            $capabilities[] = ['label' => '🔍 My Loans', 'query' => 'Check status of my recent loans'];
            $caption = "Hello! I am your SABS Assistant. What would you like to check?";
        }

        return [
            'ui_type' => 'capability_chips',
            'ui_metadata' => $capabilities,
            'caption' => $caption
        ];
    }
}
