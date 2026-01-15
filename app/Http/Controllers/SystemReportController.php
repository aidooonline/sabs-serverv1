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
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            // 1. Customer Savings Liability
            $liabilityQuery = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId);
            
            if ($isAgent) {
                $liabilityQuery->whereIn('account_number', function($query) use ($compId, $userId) {
                    $query->select('account_number')
                        ->from('nobs_registration')
                        ->where('comp_id', $compId)
                        ->where('user', $userId);
                });
            }
            $totalLiability = $liabilityQuery->sum('balance');

            // 2. Total Loan Portfolio
            $loanQuery = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereIn('status', ['active', 'disbursed']);
            
            if ($isAgent) {
                $loanQuery->where('created_by_user_id', $userId);
            }
            $totalLoanPortfolio = $loanQuery->sum('amount');
            $activeLoansCount = $loanQuery->count();

            // 3. Loan Pool Balance (Only for Management)
            $totalPoolCash = 0;
            if (!$isAgent) {
                $totalPoolCash = DB::table('central_loan_accounts')
                    ->where('comp_id', $compId)
                    ->sum('balance');
            }

            // 4. Customer Count
            $customerQuery = DB::table('nobs_registration')
                ->where('comp_id', $compId);
            
            if ($isAgent) {
                $customerQuery->where('user', $userId);
            }
            $totalCustomers = $customerQuery->count();

            // 5. Active Customers (Last 90 Days)
            $ninetyDaysAgo = Carbon::now()->subDays(90);
            $activeQuery = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('created_at', '>=', $ninetyDaysAgo);
            
            if ($isAgent) {
                $activeQuery->where('users', $userId);
            }
            $activeCustomers = $activeQuery->distinct('account_number')->count('account_number');

            // 6. Detailed Data (Loans & Commissions)
            // For Agents: Only their data. For Management: Global totals.
            $loanStatsQuery = DB::table('loan_applications')
                ->select('status', DB::raw('count(*) as total'))
                ->where('comp_id', $compId);
            
            if ($isAgent) {
                $loanStatsQuery->where('created_by_user_id', $userId);
            }
            
            $loanStats = $loanStatsQuery->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $loanData = [
                'pending' => $loanStats['pending'] ?? 0,
                'approved' => $loanStats['approved'] ?? 0,
                'rejected' => $loanStats['rejected'] ?? 0,
                'repaid' => $loanStats['repaid'] ?? 0,
                'active' => $loanStats['active'] ?? 0,
                'disbursed' => $loanStats['disbursed'] ?? 0,
            ];

            // Unpaid Commission
            $commissionQuery = DB::table('agent_commissions')
                ->where('comp_id', $compId)
                ->where('status', 'earned')
                ->whereNull('payout_id');
            
            if ($isAgent) {
                $commissionQuery->where('agent_id', $userId);
            }
            $unpaidCommission = $commissionQuery->sum('amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_liability' => round($totalLiability, 2),
                    'total_loan_portfolio' => round($totalLoanPortfolio, 2),
                    'total_pool_cash' => round($totalPoolCash, 2),
                    'total_customers' => $totalCustomers,
                    'active_customers' => $activeCustomers,
                    'active_loans_count' => $activeLoansCount,
                    'loans' => $loanData,
                    'unpaid_commission' => round($unpaidCommission, 2),
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
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $query = DB::table('nobs_transactions')
                ->join('nobs_registration', 'nobs_transactions.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_transactions.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    DB::raw('SUM(amount) as total_withdrawn')
                )
                ->where('nobs_transactions.comp_id', $compId)
                ->where('name_of_transaction', 'LIKE', '%Withdraw%')
                ->whereBetween('nobs_transactions.created_at', [$startDate, $endDate])
                ->groupBy('nobs_transactions.account_number', 'customer_name')
                ->orderBy('total_withdrawn', 'DESC');

            if ($isAgent) {
                $query->where('nobs_transactions.users', $userId);
            }

            if ($request->has('paginate')) {
                $topWithdrawals = $query->paginate(20);
            } else {
                $topWithdrawals = $query->limit(10)->get();
            }

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
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $query = DB::table('loan_applications')
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
                ->orderBy('total_borrowed', 'DESC');

            if ($isAgent) {
                $query->where('loan_applications.created_by_user_id', $userId);
            }

            if ($request->has('paginate')) {
                $topBorrowers = $query->paginate(20);
            } else {
                $topBorrowers = $query->limit(10)->get();
            }

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
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();
            $ninetyDaysAgo = Carbon::now()->subDays(90);

            $totalQuery = DB::table('nobs_registration')->where('comp_id', $compId);
            $activeQuery = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('created_at', '>=', $ninetyDaysAgo);
            
            if ($isAgent) {
                $totalQuery->where('user', $userId);
                $activeQuery->where('users', $userId);
            }

            $total = $totalQuery->count();
            $active = $activeQuery->distinct('account_number')->count('account_number');
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
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            $query = DB::table('nobs_transactions')
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
                ->orderBy('total_deposited', 'DESC');

            if ($isAgent) {
                $query->where('nobs_transactions.users', $userId);
            }

            if ($request->has('paginate')) {
                $topCustomers = $query->paginate(20);
            } else {
                $topCustomers = $query->limit(10)->get();
            }

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

        // Return zeros for Agents - they shouldn't see system revenue
        if ($this->isAgentOnly()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'interest_collected' => 0,
                    'fees_collected' => 0,
                    'commissions_collected' => 0,
                    'total_revenue' => 0
                ]
            ]);
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // Interest Collected
            $interest = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('interest_paid');

            // Fees Collected
            $feesRepaid = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('fees_paid');
            
            // Commission Revenue
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
     * Integrity Check.
     */
    public function getIntegrityReport(Request $request)
    {
        if (!$this->isManagement() || $this->isAgentOnly()) {
            return response()->json(['success' => true, 'data' => []]); // Return empty for Agents
        }

        try {
            $compId = auth()->user()->comp_id;
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

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
     * Get Top Account Balances.
     */
    public function getTopAccountBalances(Request $request)
    {
        if (!$this->isManagement()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            $query = DB::table('nobs_user_account_numbers')
                ->join('nobs_registration', 'nobs_user_account_numbers.account_number', '=', 'nobs_registration.account_number')
                ->select(
                    'nobs_user_account_numbers.account_number',
                    DB::raw('CONCAT(first_name, " ", surname) as customer_name'),
                    'nobs_user_account_numbers.balance'
                )
                ->where('nobs_user_account_numbers.comp_id', $compId)
                ->orderBy('balance', 'DESC');

            if ($isAgent) {
                $query->where('nobs_registration.user', $userId);
            }

            if ($request->has('paginate')) {
                $topAccounts = $query->paginate(20);
            } else {
                $topAccounts = $query->limit(10)->get();
            }

            return response()->json([
                'success' => true,
                'data' => $topAccounts
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper to get Top Agents.
     */
    private function getTopAgentsByTransaction($type, $request)
    {
        $compId = auth()->user()->comp_id;
        $userId = auth()->id();
        $isAgent = $this->isAgentOnly();

        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

        $query = DB::table('nobs_transactions')
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
            ->orderBy('total_amount', 'DESC');

        if ($isAgent) {
            $query->where('nobs_transactions.users', $userId);
        }

        if ($request->has('paginate')) {
            return $query->paginate(20);
        } else {
            return $query->limit(10)->get();
        }
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
            $query = DB::table('loan_applications')
                ->join('users', 'loan_applications.created_by_user_id', '=', 'users.id')
                ->select(
                    'users.name as agent_name',
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('COUNT(loan_applications.id) as transaction_count')
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereIn('status', ['active', 'disbursed', 'repaid'])
                ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                ->groupBy('users.name')
                ->orderBy('total_amount', 'DESC');

            if ($request->has('paginate')) {
                $topAgents = $query->paginate(20);
            } else {
                $topAgents = $query->limit(10)->get();
            }

            return response()->json(['success' => true, 'data' => $topAgents]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Operational Metrics (Migrated from Dashboard).
     */
    public function getOperationalMetrics(Request $request)
    {
        if (!$this->isManagement()) return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);

        try {
            $compId = auth()->user()->comp_id;
            $userId = auth()->id();
            $isAgent = $this->isAgentOnly();

            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfMonth();

            // 1. Transaction Metrics
            $metricQuery = DB::table('nobs_transactions')
                ->select(
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Deposit" THEN amount ELSE 0 END) AS total_deposits'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdraw" THEN amount ELSE 0 END) AS total_withdrawals'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Withdrawal Request" THEN amount ELSE 0 END) AS withdrawal_requests_amount'),
                    DB::raw('COUNT(CASE WHEN name_of_transaction = "Withdrawal Request" THEN 1 END) AS withdrawal_requests_count'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Agent Commission" THEN amount ELSE 0 END) AS agent_commission'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Commission" THEN amount ELSE 0 END) AS system_commission'),
                    DB::raw('SUM(CASE WHEN name_of_transaction = "Refund" THEN amount ELSE 0 END) AS total_refunds')
                )
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('is_shown', 1);
            
            if ($isAgent) {
                $metricQuery->where('users', $userId);
            }
            $metrics = $metricQuery->first();

            // 2. New Customers (Registered in period)
            $customerQuery = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            if ($isAgent) {
                $customerQuery->where('user', $userId);
            }
            $totalCustomers = $customerQuery->count();

            // 3. Balance Calculation (Deposit - Withdrawal - Refunds - Commissions)
            // Matches legacy logic: totalDP - totalWD - totalRF - totalAGTCM - totalSCM
            $balance = $metrics->total_deposits 
                     - $metrics->total_withdrawals 
                     - $metrics->total_refunds
                     - $metrics->agent_commission 
                     - $metrics->system_commission;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'total_deposits' => $metrics->total_deposits,
                    'total_withdrawals' => $metrics->total_withdrawals,
                    'withdrawal_requests_count' => $metrics->withdrawal_requests_count,
                    'withdrawal_requests_amount' => $metrics->withdrawal_requests_amount,
                    'agent_commission' => $metrics->agent_commission,
                    'system_commission' => $metrics->system_commission,
                    'balance' => $balance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isManagement()
    {
        $user = auth()->user();
        if (!$user) return false;

        $managementTypes = ['Admin', 'owner', 'super admin', 'God Admin', 'Manager', 'Agents'];
        $managementRoles = ['Admin', 'Owner', 'super admin', 'Manager', 'Agents'];

        return in_array($user->type, $managementTypes) || $user->hasRole($managementRoles);
    }

    private function isAgentOnly()
    {
        $user = auth()->user();
        $mgmtRoles = ['Admin', 'Owner', 'super admin', 'Manager'];
        
        // They are an Agent ONLY if they don't have management roles but have the Agent type/role
        return !$user->hasRole($mgmtRoles) && ($user->type === 'Agents' || $user->hasRole('Agents'));
    }
}