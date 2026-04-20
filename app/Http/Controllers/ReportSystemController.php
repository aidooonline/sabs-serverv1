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
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $user = auth()->user();
            $compId = $user->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // 1. Customer Report Group (Optimized)
            $customers = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get();

            foreach ($customers as $c) {
                $savings = DB::table('nobs_user_account_numbers')->where('account_number', $c->account_number)->where('comp_id', $compId)->sum('balance');
                $debt = DB::table('loan_applications')->where('customer_id', $c->id)->whereIn('status', ['active', 'disbursed', 'defaulted'])->sum(DB::raw('total_repayment - total_paid'));
                $c->net_balance = round($savings - $debt, 2);
                $c->savings_total = round($savings, 2);
                $c->debt_total = round($debt, 2);
            }

            // 2. Deposit/Withdrawal Totals
            $deposits = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Deposit')->whereBetween('created_at', [$startDate, $endDate]);
            $withdrawals = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Withdraw')->whereBetween('created_at', [$startDate, $endDate]);
            
            $depositSummary = [
                'total_amount' => round($deposits->sum('amount'), 2),
                'total_count' => $deposits->count(),
                'list' => $deposits->orderBy('created_at', 'desc')->limit(50)->get()
            ];
            $withdrawalSummary = [
                'total_amount' => round($withdrawals->sum('amount'), 2),
                'total_count' => $withdrawals->count(),
                'list' => $withdrawals->orderBy('created_at', 'desc')->limit(50)->get()
            ];

            // 3. Loan Portfolio (Fixed Ambiguity)
            $loans = DB::table('loan_applications')
                ->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                ->join('loan_products', 'loan_applications.loan_product_id', '=', 'loan_products.id')
                ->select(
                    'loan_applications.id',
                    'loan_applications.amount',
                    'loan_applications.total_repayment',
                    'loan_applications.total_paid',
                    'loan_applications.status',
                    'loan_applications.created_at',
                    'nobs_registration.first_name',
                    'nobs_registration.surname',
                    'nobs_registration.account_number',
                    'loan_products.name as product_name'
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                ->orderBy('loan_applications.created_at', 'desc')
                ->limit(100)
                ->get();

            foreach ($loans as $l) {
                $l->customer_name = "{$l->first_name} {$l->surname}";
                $l->outstanding_balance = round($l->total_repayment - $l->total_paid, 2);
                $l->loan_product = ['name' => $l->product_name];
            }

            return response()->json([
                'success' => true,
                'summary' => [
                    'customers' => ['total_customers' => DB::table('nobs_registration')->where('comp_id', $compId)->count(), 'new_registrations' => $customers->count(), 'list' => $customers],
                    'deposits' => $depositSummary,
                    'withdrawals' => $withdrawalSummary,
                    'loans' => ['total_disbursed' => round(DB::table('loan_applications')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'), 2), 'list' => $loans],
                    'analysis' => ['liquidity' => round($depositSummary['total_amount'] - $withdrawalSummary['total_amount'], 2)]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $token = $request->query('token');
            $user = User::where('api_token', $token)->first();
            if (!$user) return response('Unauthorized', 401);
            $compId = $user->comp_id;

            $month = $request->query('month');
            $year = $request->query('year');
            $type = $request->query('type', 'deposits'); 
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $filename = "SABS_Report_{$type}_{$month}_{$year}.csv";
            $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];

            $callback = function() use ($type, $compId, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                if ($type == 'deposits' || $type == 'withdrawals') {
                    fputcsv($file, ['Date', 'Customer', 'Account', 'Amount', 'Resulting Balance', 'Reference']);
                    $data = DB::table('accounts_transactions')->where('comp_id', $compId)->where('name_of_transaction', $type == 'deposits' ? 'Deposit' : 'Withdraw')->whereBetween('created_at', [$startDate, $endDate])->get();
                    foreach ($data as $row) fputcsv($file, [$row->created_at, $row->det_rep_name_of_transaction, $row->account_number, $row->amount, $row->balance, $row->transaction_id]);
                } elseif ($type == 'loans') {
                    fputcsv($file, ['Date', 'Loan ID', 'Customer', 'Principal', 'Total Repayable', 'Total Paid', 'Balance', 'Status']);
                    $data = DB::table('loan_applications')->join('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')->select('loan_applications.*', 'nobs_registration.first_name', 'nobs_registration.surname')->where('loan_applications.comp_id', $compId)->whereBetween('loan_applications.created_at', [$startDate, $endDate])->get();
                    foreach ($data as $row) fputcsv($file, [$row->created_at, $row->id, "{$row->first_name} {$row->surname}", $row->amount, $row->total_repayment, $row->total_paid, ($row->total_repayment - $row->total_paid), $row->status]);
                } else {
                    fputcsv($file, ['Date', 'Customer', 'Account', 'Savings Total', 'Debt Total', 'Net Global Balance']);
                    $data = DB::table('nobs_registration')->where('comp_id', $compId)->whereBetween('created_at', [$startDate, $endDate])->get();
                    foreach ($data as $c) {
                        $savings = DB::table('nobs_user_account_numbers')->where('account_number', $c->account_number)->where('comp_id', $compId)->sum('balance');
                        $debt = DB::table('loan_applications')->where('customer_id', $c->id)->whereIn('status', ['active', 'disbursed', 'defaulted'])->sum(DB::raw('total_repayment - total_paid'));
                        fputcsv($file, [$c->created_at, "{$c->first_name} {$c->surname}", $c->account_number, $savings, $debt, ($savings - $debt)]);
                    }
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) { return response('Export Error: ' . $e->getMessage(), 500); }
    }

    public function saveSnapshot(Request $request) { return response()->json(['success' => true]); }
}
