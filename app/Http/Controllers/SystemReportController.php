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

            // 5. Active Customers (Last 90 Days) - Based on account_status from Scheduler
            $activeCustomers = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId)
                ->where('account_status', 'active')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_liability' => round($totalLiability, 2),
                    'total_loan_portfolio' => round($totalLoanPortfolio, 2),
                    'total_pool_cash' => round($totalPoolCash, 2),
                    'total_customers' => $totalCustomers,
                    'active_customers' => $activeCustomers,
                    'last_updated' => now()->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Top Withdrawals for a specific date range.
     */
    public function getTopWithdrawals(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $topWithdrawals = DB::table('nobs_transactions')
                ->join('nobs_registration', 'nobs_transactions.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_transactions.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    DB::raw('SUM(amount) as total_withdrawn')
                )
                ->where('nobs_transactions.comp_id', $compId)
                ->whereIn('name_of_transaction', ['Withdraw', 'Withdrawal'])
                ->whereBetween('nobs_transactions.created_at', [$startDate, $endDate])
                ->groupBy('nobs_transactions.account_number', 'customer_name')
                ->orderBy('total_withdrawn', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topWithdrawals,
                'period' => $startDate->toDateString() . ' to ' . $endDate->toDateString()
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Top Borrowers (Loan Applicants) for a specific date range.
     */
    public function getTopBorrowers(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $topBorrowers = DB::table('loan_applications')
                ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                ->select(
                    'nobs_registration.account_number',
                    DB::raw('CONCAT(nobs_registration.first_name, " ", nobs_registration.surname) as customer_name'),
                    DB::raw('SUM(loan_applications.amount) as total_borrowed'),
                    DB::raw('COUNT(loan_applications.id) as loan_count')
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereIn('loan_applications.status', ['active', 'disbursed', 'repaid']) // Only count approved loans
                ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                ->groupBy('nobs_registration.account_number', 'customer_name')
                ->orderBy('total_borrowed', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topBorrowers,
                'period' => $startDate->toDateString() . ' to ' . $endDate->toDateString()
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
     * Get Top Depositors for a specific date range.
     */
    public function getTopCustomers(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $topCustomers = DB::table('nobs_transactions')
                ->join('nobs_registration', 'nobs_transactions.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_transactions.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    DB::raw('SUM(amount) as total_deposited')
                )
                ->where('nobs_transactions.comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->whereBetween('nobs_transactions.created_at', [$startDate, $endDate])
                ->groupBy('nobs_transactions.account_number', 'customer_name')
                ->orderBy('total_deposited', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topCustomers,
                'period' => $startDate->toDateString() . ' to ' . $endDate->toDateString()
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Financial Performance (Interest & Fees) for a date range.
     */
    public function getFinancialPerformance(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // Interest Collected
            $interest = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate]) // Assuming updated_at reflects payment time
                ->sum('interest_paid');

            // Fees Collected (from Repayments)
            $feesRepaid = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('fees_paid');
            
            // Commission Revenue (What the company kept from withdrawals/deposits if applicable)
            $commissions = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Commission')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'interest_collected' => round($interest, 2),
                    'fees_collected' => round($feesRepaid, 2),
                    'commissions_collected' => round($commissions, 2),
                    'total_revenue' => round($interest + $feesRepaid + $commissions, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Integrity Check: Detect duplicate commission deductions in the date range.
     */
    public function getIntegrityReport(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // Find accounts with > 1 'Commission' transaction in the period
            $duplicates = DB::table('nobs_transactions')
                ->select('account_number', DB::raw('count(*) as deduction_count'), DB::raw('SUM(amount) as total_deducted'))
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Commission')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('account_number')
                ->having('deduction_count', '>', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $duplicates,
                'check_period' => $startDate->toDateString() . ' to ' . $endDate->toDateString()
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