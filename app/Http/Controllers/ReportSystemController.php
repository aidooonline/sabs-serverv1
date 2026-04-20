<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Accounts;
use App\AccountsTransactions;
use App\LoanApplication;
use App\LoanRepaymentSchedule;

class ReportSystemController extends Controller
{
    /**
     * Get Report Hub Data (Live Preview)
     */
    public function getLiveReport(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        $user = auth()->user();
        $compId = $user->comp_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 1. Customer Report Group
        $customerSummary = [
            'total_customers' => Accounts::where('comp_id', $compId)->count(),
            'new_registrations' => Accounts::where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'by_gender' => Accounts::where('comp_id', $compId)
                ->select('gender', DB::raw('count(*) as total'))
                ->groupBy('gender')
                ->get()
        ];

        // 2. Deposit Report Group
        $deposits = AccountsTransactions::where('comp_id', $compId)
            ->where('name_of_transaction', 'Deposit')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $depositSummary = [
            'total_amount' => $deposits->sum('amount'),
            'total_count' => $deposits->count(),
            'list' => $deposits->orderBy('created_at', 'desc')->limit(100)->get()
        ];

        // 3. Withdrawal Report Group
        $withdrawals = AccountsTransactions::where('comp_id', $compId)
            ->where('name_of_transaction', 'Withdraw')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $withdrawalSummary = [
            'total_amount' => $withdrawals->sum('amount'),
            'total_count' => $withdrawals->count(),
            'list' => $withdrawals->orderBy('created_at', 'desc')->limit(100)->get()
        ];

        // 4. Loan Portfolio Group
        $loans = LoanApplication::where('comp_id', $compId)
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        $repayments = LoanRepaymentSchedule::where('comp_id', $compId)
            ->whereBetween('updated_at', [$startDate, $endDate]);

        $loanSummary = [
            'total_disbursed' => $loans->sum('amount'),
            'repayments_collected' => $repayments->sum('total_paid'),
            'interest_collected' => $repayments->sum('interest_paid'),
            'fees_collected' => $repayments->sum('fees_paid'),
            'list' => $loans->with('loan_product')->limit(100)->get()
        ];

        // 5. Cross-Matching (Intelligence)
        $crossMatch = [
            'liquidity' => round($depositSummary['total_amount'] - $withdrawalSummary['total_amount'], 2),
            'loan_to_deposit_ratio' => $depositSummary['total_amount'] > 0 
                ? round(($loanSummary['total_disbursed'] / $depositSummary['total_amount']) * 100, 2) 
                : 0,
            'collection_efficiency' => $loanSummary['total_disbursed'] > 0
                ? round(($loanSummary['repayments_collected'] / $loanSummary['total_disbursed']) * 100, 2)
                : 0
        ];

        return response()->json([
            'success' => true,
            'summary' => [
                'customers' => $customerSummary,
                'deposits' => $depositSummary,
                'withdrawals' => $withdrawalSummary,
                'loans' => $loanSummary,
                'analysis' => $crossMatch
            ]
        ], 200);
    }

    /**
     * Archive Current Month into Snapshots
     */
    public function saveSnapshot(Request $request)
    {
        // Logic to move data to report_snapshots and report_snapshot_items
        // Similar to above but with DB inserts
    }

    /**
     * Export Report to CSV
     */
    public function exportCsv(Request $request)
    {
        // Logic to generate CSV for Excel
    }
}
