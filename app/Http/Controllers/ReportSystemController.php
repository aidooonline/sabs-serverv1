<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Accounts;
use App\AccountsTransactions;
use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\User;

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

            // 1. Customer Report Group (Enhanced with Global Balance)
            $customers = Accounts::where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            foreach ($customers as $c) {
                // Calculate Net Global Balance (Savings - Debt)
                $savings = DB::table('nobs_user_account_numbers')
                    ->where('account_number', $c->account_number)
                    ->where('comp_id', $compId)
                    ->sum('balance');
                
                $debt = DB::table('loan_applications')
                    ->where('customer_id', $c->id)
                    ->whereIn('status', ['active', 'disbursed', 'defaulted'])
                    ->select(DB::raw('(total_repayment - total_paid) as balance'))
                    ->get()
                    ->sum('balance');
                
                $c->net_balance = round($savings - $debt, 2);
                $c->savings_total = round($savings, 2);
                $c->debt_total = round($debt, 2);
            }

            $customerSummary = [
                'total_customers' => Accounts::where('comp_id', $compId)->count(),
                'new_registrations' => $customers->count(),
                'list' => $customers
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
            $withdrawalQuery = AccountsTransactions::where('comp_id', $compId)
                ->where('name_of_transaction', 'Withdraw')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $withdrawalSummary = [
                'total_amount' => round($withdrawalQuery->sum('amount'), 2),
                'total_count' => $withdrawalQuery->count(),
                'list' => $withdrawalQuery->orderBy('created_at', 'desc')->limit(100)->get()
            ];

            // 4. Loan Portfolio Group (Enhanced with Breakdown)
            $loans = LoanApplication::with('loan_product')
                ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                ->select('loan_applications.*', 'nobs_registration.first_name', 'nobs_registration.surname')
                ->where('loan_applications.comp_id', $compId)
                ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                ->orderBy('loan_applications.created_at', 'desc')
                ->limit(100)
                ->get();

            foreach ($loans as $l) {
                $l->customer_name = "{$l->first_name} {$l->surname}";
                $l->amount_paid = DB::table('loan_repayment_schedules')->where('loan_application_id', $l->id)->sum('total_paid');
                $l->outstanding_balance = round($l->total_repayment - $l->amount_paid, 2);
            }

            $loanSummary = [
                'total_disbursed' => round($loans->sum('amount'), 2),
                'repayments_collected' => round($loans->sum('amount_paid'), 2),
                'list' => $loans
            ];

            // 5. Cross-Matching (Intelligence)
            $crossMatch = [
                'liquidity' => round($depositSummary['total_amount'] - $withdrawalSummary['total_amount'], 2),
                'loan_to_deposit_ratio' => $depositSummary['total_amount'] > 0 
                    ? round(($loanSummary['total_disbursed'] / $depositSummary['total_amount']) * 100, 2) 
                    : 0,
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
            DB::table('report_snapshots')->where(['comp_id' => $compId, 'period_month' => $month, 'period_year' => $year])->delete();

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $deposits = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Deposit')->whereBetween('created_at', [$startDate, $endDate]);
            $withdrawals = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Withdraw')->whereBetween('created_at', [$startDate, $endDate]);
            $loans = DB::table('loan_applications')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate]);

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
                'generated_by_user_id' => $user->id,
                'created_at' => now()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Monthly snapshot archived successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export Report to CSV (Token-based Auth Fallback)
     */
    public function exportCsv(Request $request)
    {
        $token = $request->query('token');
        if ($token) {
            // Manual verification for browser downloads
            $user = User::where('api_token', $token)->first();
            if (!$user) return response('Unauthorized', 401);
            $compId = $user->comp_id;
        } else {
            $user = auth()->user();
            if (!$user) return response('Unauthorized', 401);
            $compId = $user->comp_id;
        }

        $month = $request->query('month');
        $year = $request->query('year');
        $type = $request->query('type', 'deposits'); 

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
            } elseif ($type == 'loans') {
                fputcsv($file, ['Date', 'Loan ID', 'Customer', 'Principal', 'Total Repayable', 'Total Paid', 'Balance', 'Status']);
                $data = DB::table('loan_applications')
                    ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                    ->select('loan_applications.*', 'nobs_registration.first_name', 'nobs_registration.surname')
                    ->where('loan_applications.comp_id', $compId)
                    ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                    ->get();
                foreach ($data as $row) {
                    $paid = DB::table('loan_repayment_schedules')->where('loan_application_id', $row->id)->sum('total_paid');
                    fputcsv($file, [
                        $row->created_at, $row->id, "{$row->first_name} {$row->surname}", 
                        $row->amount, $row->total_repayment, $paid, ($row->total_repayment - $paid), $row->status
                    ]);
                }
            } else {
                fputcsv($file, ['Date', 'Customer', 'Account', 'Savings Bal', 'Loan Debt', 'Net Global Balance']);
                $data = DB::table('nobs_registration')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate])->get();
                foreach ($data as $c) {
                    $savings = DB::table('nobs_user_account_numbers')->where('account_number', $c->account_number)->where('comp_id', $compId)->sum('balance');
                    $debt = DB::table('loan_applications')->where('customer_id', $c->id)->whereIn('status', ['active', 'disbursed', 'defaulted'])->select(DB::raw('(total_repayment - total_paid) as balance'))->get()->sum('balance');
                    fputcsv($file, [$c->created_at, "{$c->first_name} {$c->surname}", $c->account_number, $savings, $debt, ($savings - $debt)]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
