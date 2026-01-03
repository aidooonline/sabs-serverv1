<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemReportController extends Controller
{
    /**
     * Get high-level financial health metrics.
     */
    public function getExecutiveSummary()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;

            // 1. Total Liability (What the company owes customers in savings)
            $totalLiability = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId)
                ->sum('balance');

            // 2. Total Loan Portfolio (Principal currently out with customers)
            // Simplified: Query loan_applications directly using the new comp_id column (Sprint 10 Patch)
            $totalLoanPortfolio = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereIn('status', ['active', 'disbursed'])
                ->sum('amount');

            // 3. Total Pool Cash (Available cash in the loan system)
            $totalPoolCash = DB::table('central_loan_accounts')
                ->where('comp_id', $compId)
                ->sum('balance');

            // 4. Customer Count
            $totalCustomers = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_liability' => round($totalLiability, 2),
                    'total_loan_portfolio' => round($totalLoanPortfolio, 2),
                    'total_pool_cash' => round($totalPoolCash, 2),
                    'total_customers' => $totalCustomers,
                    'last_updated' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get counts of active vs dormant accounts.
     */
    public function getDormancyStats()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;

            $stats = DB::table('nobs_user_account_numbers')
                ->select('account_status', DB::raw('count(*) as total'))
                ->where('comp_id', $compId)
                ->groupBy('account_status')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Integrity Check: Detect duplicate commission deductions in the current month.
     */
    public function getIntegrityReport()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Find accounts with > 1 'Commission' transaction this month
            $duplicates = DB::table('nobs_transactions')
                ->select('account_number', DB::raw('count(*) as deduction_count'), DB::raw('SUM(amount) as total_deducted'))
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Commission')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->groupBy('account_number')
                ->having('deduction_count', '>', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $duplicates,
                'check_period' => Carbon::now()->format('F Y')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Top 10 Depositors for the current month.
     */
    public function getTopCustomers()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startOfMonth = Carbon::now()->startOfMonth();

            $topCustomers = DB::table('nobs_transactions')
                ->join('nobs_registration', 'nobs_transactions.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_transactions.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    DB::raw('SUM(amount) as total_deposited')
                )
                ->where('nobs_transactions.comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->where('nobs_transactions.created_at', '>=', $startOfMonth)
                ->groupBy('nobs_transactions.account_number', 'customer_name')
                ->orderBy('total_deposited', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topCustomers
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isManagement()
    {
        $user = auth()->user();
        if (!$user) return false;

        $managementTypes = ['Admin', 'owner', 'super admin', 'God Admin', 'Manager'];
        $managementRoles = ['Admin', 'Owner', 'super admin', 'Manager'];

        return in_array($user->type, $managementTypes) || $user->hasRole($managementRoles);
    }
}
