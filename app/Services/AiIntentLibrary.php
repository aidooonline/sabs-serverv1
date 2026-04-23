<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * AiIntentLibrary - Secure, pre-verified data fetchers for the AI Assistant.
 * ZERO surprise SQL allowed.
 */
class AiIntentLibrary
{
    private $compId;

    public function __construct($compId = null)
    {
        $this->compId = $compId ?: Auth::user()->comp_id;
    }

    /**
     * Intent: Total Deposits / Total Withdrawals
     */
    public function getFinancialSummary($type = 'Deposit', $date = null)
    {
        $date = $date ?: date('Y-m-d');
        
        $data = DB::table('nobs_transactions')
            ->select(
                DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->where('comp_id', $this->compId)
            ->where('name_of_transaction', $type)
            ->whereDate('created_at', $date)
            ->first();

        $captionDate = ($date === date('Y-m-d')) ? 'today' : "on $date";

        return [
            'ui_type' => 'summary_stat_card',
            'ui_metadata' => [
                'title' => "Total " . ($type === 'Withdraw' ? 'Withdrawals' : 'Deposits') . " ($captionDate)",
                'value' => number_format($data->total_amount, 2),
                'suffix' => 'GHS',
                'details' => "Count: " . $data->count . " transactions"
            ],
            'caption' => "I found " . $data->count . " " . strtolower($type) . "s $captionDate totaling GHS " . number_format($data->total_amount, 2) . "."
        ];
    }

    /**
     * Intent: Customer Search
     */
    public function searchCustomers($term)
    {
        $customers = DB::table('nobs_registration')
            ->select('id', 'first_name', 'surname', 'account_number', 'phone_number')
            ->where('comp_id', $this->compId)
            ->where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%$term%")
                  ->orWhere('surname', 'LIKE', "%$term%")
                  ->orWhere('account_number', 'LIKE', "%$term%")
                  ->orWhere('phone_number', 'LIKE', "%$term%");
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
     * Intent: Loan Status Overview
     */
    public function getLoanOverview()
    {
        $stats = DB::table('loan_applications')
            ->select('status', DB::raw('count(*) as count'), DB::raw('SUM(amount) as total_sum'))
            ->where('comp_id', $this->compId)
            ->groupBy('status')
            ->get();

        return [
            'ui_type' => 'data_table',
            'ui_metadata' => $stats,
            'caption' => "Here is your current loan portfolio breakdown."
        ];
    }

    /**
     * Intent: Capability Help
     */
    public function getHelpMenu()
    {
        return [
            'ui_type' => 'capability_chips',
            'ui_metadata' => [
                ['label' => 'Today\'s Deposits', 'query' => 'Show me total deposits for today'],
                ['label' => 'Find Customer', 'query' => 'Search for a customer'],
                ['label' => 'Active Loans', 'query' => 'Show loan status overview'],
                ['label' => 'Recent Activity', 'query' => 'Show me the last 5 transactions']
            ],
            'caption' => "I can help you manage your bank. Select an action below:"
        ];
    }

    /**
     * Intent: Recent Ledger Activity
     */
    public function getRecentActivity($limit = 5)
    {
        $data = DB::table('nobs_transactions')
            ->select('name_of_transaction as type', 'amount', 'account_number', 'created_at')
            ->where('comp_id', $this->compId)
            ->where('is_shown', 1)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        return [
            'ui_type' => 'data_table',
            'ui_metadata' => $data,
            'caption' => "Here are the $limit most recent transactions."
        ];
    }
}
