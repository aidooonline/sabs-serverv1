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
        try {
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
                'list' => Accounts::where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
            ];

            // 2. Deposit Report Group
            $deposits = AccountsTransactions::where('comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $depositSummary = [
                'total_amount' => round($deposits->sum('amount'), 2),
                'total_count' => $deposits->count(),
                'list' => $deposits->orderBy('created_at', 'desc')->limit(100)->get()
            ];

            // 3. Withdrawal Report Group
            $withdrawals = AccountsTransactions::where('comp_id', $compId)
                ->where('name_of_transaction', 'Withdraw')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $withdrawalSummary = [
                'total_amount' => round($withdrawals->sum('amount'), 2),
                'total_count' => $withdrawals->count(),
                'list' => $withdrawals->orderBy('created_at', 'desc')->limit(100)->get()
            ];

            // 4. Loan Portfolio Group
            $loans = LoanApplication::where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $repayments = LoanRepaymentSchedule::where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate]);

            $loanSummary = [
                'total_disbursed' => round($loans->sum('amount'), 2),
                'repayments_collected' => round($repayments->sum('total_paid'), 2),
                'interest_collected' => round($repayments->sum('interest_paid'), 2),
                'fees_collected' => round($repayments->sum('fees_paid'), 2),
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
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Archive Current Month into Snapshots
     */
    public function saveSnapshot(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $user = auth()->user();
        $compId = $user->comp_id;

        DB::beginTransaction();
        try {
            // Check if exists
            DB::table('report_snapshots')
                ->where(['comp_id' => $compId, 'period_month' => $month, 'period_year' => $year])
                ->delete();

            // 1. Generate Totals (similar to getLiveReport)
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $deposits = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Deposit')->whereBetween('created_at', [$startDate, $endDate]);
            $withdrawals = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Withdraw')->whereBetween('created_at', [$startDate, $endDate]);
            $loans = DB::table('loan_applications')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate]);
            $repayments = DB::table('loan_repayment_schedules')->where('comp_id', $compId)->whereBetween('updated_at', [$startDate, $endDate]);

            $snapshotId = DB::table('report_snapshots')->insertGetId([
                'comp_id' => $compId,
                'period_month' => $month,
                'period_year' => $year,
                'total_customers_count' => DB::table('nobs_registration')->where('comp_id', $compId)->count(),
                'new_registrations' => DB::table('nobs_registration')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_deposit_amount' => $deposits->sum('amount') ?? 0,
                'total_deposit_count' => $deposits->count(),
                'total_withdrawal_amount' => $withdrawals->sum('amount') ?? 0,
                'total_withdrawal_count' => $withdrawals->count(),
                'net_cash_flow' => ($deposits->sum('amount') ?? 0) - ($withdrawals->sum('amount') ?? 0),
                'total_disbursed_amount' => $loans->sum('amount') ?? 0,
                'total_repayments_collected' => $repayments->sum('total_paid') ?? 0,
                'interest_collected' => $repayments->sum('interest_paid') ?? 0,
                'fees_collected' => $repayments->sum('fees_paid') ?? 0,
                'generated_by_user_id' => $user->id,
                'created_at' => now()
            ]);

            // 2. Archive items (Simplified for performance, archiving main financial movements)
            $txs = DB::table('accounts_transactions')
                ->where('comp_id', $compId)
                ->whereIn('name_of_transaction', ['Deposit', 'Withdraw'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            foreach ($txs as $tx) {
                DB::table('report_snapshot_items')->insert([
                    'snapshot_id' => $snapshotId,
                    'item_group' => strtolower($tx->name_of_transaction),
                    'customer_name' => $tx->det_rep_name_of_transaction,
                    'account_number' => $tx->account_number,
                    'amount' => $tx->amount,
                    'balance_at_time' => $tx->balance,
                    'transaction_date' => $tx->created_at,
                    'reference_id' => $tx->transaction_id
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Monthly snapshot archived successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export Report to CSV
     */
    public function exportCsv(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');
        $type = $request->query('type', 'deposits'); // deposits, withdrawals, loans
        $compId = auth()->user()->comp_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $filename = "SABS_Report_{$type}_{$month}_{$year}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($type, $compId, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            if ($type == 'deposits' || $type == 'withdrawals') {
                fputcsv($file, ['Date', 'Customer', 'Account', 'Amount', 'Resulting Balance', 'Reference']);
                $data = DB::table('accounts_transactions')
                    ->where('comp_id', $compId)
                    ->where('name_of_transaction', $type == 'deposits' ? 'Deposit' : 'Withdraw')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
                foreach ($data as $row) {
                    fputcsv($file, [$row->created_at, $row->det_rep_name_of_transaction, $row->account_number, $row->amount, $row->balance, $row->transaction_id]);
                }
            } else {
                fputcsv($file, ['Date', 'Loan ID', 'Principal', 'Total Repayable', 'Status']);
                $data = DB::table('loan_applications')
                    ->where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
                foreach ($data as $row) {
                    fputcsv($file, [$row->created_at, $row->id, $row->amount, $row->total_repayment, $row->status]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
