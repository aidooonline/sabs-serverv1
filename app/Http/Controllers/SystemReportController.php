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

            // 1. Customer Savings Liability (What the company owes customers in savings)
            $totalLiability = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId)
                ->sum('balance');

            // 2. Total Loan Portfolio (Principal currently out with customers)
            $totalLoanPortfolio = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereIn('status', ['active', 'disbursed'])
                ->sum('amount');

            // 3. Loan Pool Balance (Available cash in the central loan account)
            $totalPoolCash = DB::table('central_loan_accounts')
                ->where('comp_id', $compId)
                ->sum('balance');

            // 4. Customer Count (Total Registered)
            $totalCustomers = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->count();

            // 5. Active Customers (Last 90 Days)
            // Fix: Count DISTINCT accounts that have transacted in the last 90 days.
            // This ignores the "zero balance" reset and focuses on actual activity.
            $ninetyDaysAgo = Carbon::now()->subDays(90);
            
            $activeCustomers = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('created_at', '>=', $ninetyDaysAgo)
                ->distinct('account_number')
                ->count('account_number');

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
                ->where('name_of_transaction', 'LIKE', '%Withdraw%') // Catch 'Withdraw', 'Withdrawal', 'Cash Withdrawal' etc.
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
                ->whereIn('loan_applications.status', ['active', 'disbursed', 'repaid'])
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
     * Note: This endpoint might be redundant if Executive Summary handles it, 
     * but we keep it for specific charts if needed.
     */
    public function getDormancyStats()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Reusing logic from Executive Summary for consistency
            $compId = auth()->user()->comp_id;
            $ninetyDaysAgo = Carbon::now()->subDays(90);

            $total = DB::table('nobs_registration')->where('comp_id', $compId)->count();
            
            $active = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('created_at', '>=', $ninetyDaysAgo)
                ->distinct('account_number')
                ->count('account_number');
                
            $dormant = $total - $active;

            return response()->json([
                'success' => true,
                'data' => [
                    ['account_status' => 'active', 'total' => $active],
                    ['account_status' => 'dormant', 'total' => $dormant]
                ]
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

            // Interest Collected (from Loan Repayments)
            $interest = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('interest_paid');

            // Fees Collected (from Loan Repayments)
            // Ensure we are tracking 'fees_paid' correctly. 
            $feesRepaid = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('fees_paid');
            
            // Commission Revenue (What the company kept from withdrawals/deposits)
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

    /**
     * Get Top Account Balances (Highest Savings).
     */
    public function getTopAccountBalances()
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;

            $topAccounts = DB::table('nobs_user_account_numbers')
                ->join('nobs_registration', 'nobs_user_account_numbers.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_user_account_numbers.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    'nobs_user_account_numbers.balance'
                )
                ->where('nobs_user_account_numbers.comp_id', $compId)
                ->orderBy('balance', 'DESC')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topAccounts
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper to get Top Agents by Transaction Type.
     */
    private function getTopAgentsByTransaction($type, $request)
    {
        $compId = auth()->user()->comp_id;
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        // Use LIKE for robust matching (e.g., 'Withdraw' vs 'Withdrawal')
        return DB::table('nobs_transactions')
            ->join('users', 'nobs_transactions.users', '=', 'users.id')
            ->select(
                'users.name as agent_name',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(nobs_transactions.id) as transaction_count')
            )
            ->where('nobs_transactions.comp_id', $compId)
            ->where('name_of_transaction', 'LIKE', "%{$type}%") 
            ->whereBetween('nobs_transactions.created_at', [$startDate, $endDate])
            ->groupBy('users.name')
            ->orderBy('total_amount', 'DESC')
            ->limit(10)
            ->get();
    }

    public function getTopAgentDeposits(Request $request)
    {
        if (!$this->isManagement()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        try {
            $data = $this->getTopAgentsByTransaction('Deposit', $request);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTopAgentWithdrawals(Request $request)
    {
        if (!$this->isManagement()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        try {
            $data = $this->getTopAgentsByTransaction('Withdraw', $request); 
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTopAgentRepayments(Request $request)
    {
        if (!$this->isManagement()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        try {
            $data = $this->getTopAgentsByTransaction('Loan Repayment', $request);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTopAgentDisbursals(Request $request)
    {
        if (!$this->isManagement()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // This returns ALL users who disbursed loans, including Admins.
            $topAgents = DB::table('loan_applications')
                ->join('users', 'loan_applications.created_by_user_id', '=', 'users.id')
                ->select(
                    'users.name as agent_name',
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('COUNT(loan_applications.id) as transaction_count')
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereIn('status', ['active', 'disbursed', 'repaid'])
                ->whereBetween('loan_applications.repayment_start_date', [$startDate, $endDate])
                ->groupBy('users.name')
                ->orderBy('total_amount', 'DESC')
                ->limit(10)
                ->get();

            return response()->json(['success' => true, 'data' => $topAgents]);

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