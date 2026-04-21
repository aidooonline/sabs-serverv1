<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Accounts;
use App\AccountsTransactions;
use App\LoanApplication;
use App\User;
use App\UserAccountNumbers;

class ReportSystemController extends Controller
{
    /**
     * Advanced Live Report: Grouped BI Metrics
     * Designed for business owners to see growth and liquidity.
     */
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // --- 1. CUSTOMER VITALITY ---
            $customerMetrics = [
                'total_customers' => DB::table('nobs_registration')->where('comp_id', $compId)->count(),
                'new_registrations' => DB::table('nobs_registration')
                    ->where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'list' => DB::table('nobs_registration')
                    ->leftJoin('nobs_user_account_numbers', function($join) {
                        $join->on('nobs_registration.account_number', '=', 'nobs_user_account_numbers.account_number')
                             ->on('nobs_registration.comp_id', '=', 'nobs_user_account_numbers.comp_id');
                    })
                    ->select(
                        'nobs_registration.*',
                        DB::raw('IFNULL(nobs_user_account_numbers.balance, 0) as savings_total'),
                        DB::raw('(SELECT IFNULL(SUM(amount - amount_paid), 0) FROM loan_applications WHERE customer_id = nobs_registration.id AND status = "active") as debt_total')
                    )
                    ->where('nobs_registration.comp_id', $compId)
                    ->whereBetween('nobs_registration.created_at', [$startDate, $endDate])
                    ->orderBy('nobs_registration.created_at', 'DESC')
                    ->limit(50)->get()
            ];

            // --- 2. CASH FLOW (DEPOSITS & WITHDRAWALS) ---
            $deposits = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $withdrawals = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Withdraw')
                ->whereBetween('created_at', [$startDate, $endDate]);

            // --- 3. LOAN PORTFOLIO HEALTH ---
            $loans = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate]);

            $interestCollected = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('interest_paid');

            $feesCollected = DB::table('loan_repayment_schedules')
                ->where('comp_id', $compId)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->sum('fees_paid');

            // --- 4. BUSINESS INTELLIGENCE ANALYSIS ---
            $totalDepositAmt = $deposits->sum('amount');
            $totalWithdrawAmt = $withdrawals->sum('amount');
            $liquidity = $totalDepositAmt - $totalWithdrawAmt;
            
            $ldRatio = 0;
            if ($totalDepositAmt > 0) {
                $ldRatio = round(($loans->sum('amount') / $totalDepositAmt) * 100, 2);
            }

            return response()->json([
                'success' => true,
                'summary' => [
                    'customers' => $customerMetrics,
                    'deposits' => [
                        'total_amount' => round($totalDepositAmt, 2),
                        'total_count' => $deposits->count(),
                        'list' => $deposits->orderBy('created_at', 'DESC')->limit(50)->get()
                    ],
                    'withdrawals' => [
                        'total_amount' => round($totalWithdrawAmt, 2),
                        'total_count' => $withdrawals->count(),
                        'list' => $withdrawals->orderBy('created_at', 'DESC')->limit(50)->get()
                    ],
                    'loans' => [
                        'total_disbursed' => round($loans->sum('amount'), 2),
                        'interest_collected' => round($interestCollected, 2),
                        'fees_collected' => round($feesCollected, 2),
                        'list' => $loans->orderBy('created_at', 'DESC')->limit(50)->get()
                    ],
                    'analysis' => [
                        'liquidity' => round($liquidity, 2),
                        'loan_to_deposit_ratio' => $ldRatio
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Report Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Save Snapshot: Freezes current month metrics for trend analysis.
     */
    public function saveSnapshot(Request $request)
    {
        DB::beginTransaction();
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            $compId = auth()->user()->comp_id;

            // Fetch current summary using our internal logic
            $liveDataResponse = $this->getLiveReport($request);
            $liveData = $liveDataResponse->getData()->summary;

            // 1. Create Snapshot Record
            $snapshotId = DB::table('report_snapshots')->updateOrInsert(
                ['comp_id' => $compId, 'period_month' => $month, 'period_year' => $year],
                [
                    'total_customers_count' => $liveData->customers->total_customers,
                    'new_registrations' => $liveData->customers->new_registrations,
                    'total_deposit_amount' => $liveData->deposits->total_amount,
                    'total_deposit_count' => $liveData->deposits->total_count,
                    'total_withdrawal_amount' => $liveData->withdrawals->total_amount,
                    'total_withdrawal_count' => $liveData->withdrawals->total_count,
                    'net_cash_flow' => $liveData->analysis->liquidity,
                    'total_disbursed_amount' => $liveData->loans->total_disbursed,
                    'interest_collected' => $liveData->loans->interest_collected,
                    'fees_collected' => $liveData->loans->fees_collected,
                    'generated_by_user_id' => auth()->id(),
                    'created_at' => now()
                ]
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => "Snapshot for $month/$year saved successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Snapshot Failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export CSV: Generates a clean Excel-ready file for the period.
     */
    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'deposits');
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        $compId = auth()->user()->comp_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $fileName = "SABS_{$type}_Report_{$month}_{$year}.csv";
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = ['Date', 'Account', 'Name', 'Amount', 'Type', 'Agent'];
        if ($type === 'customers') {
            $columns = ['Reg Date', 'Account', 'Name', 'Phone', 'Gender', 'Agent'];
        }

        $callback = function() use($type, $compId, $startDate, $endDate, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            if ($type === 'deposits' || $type === 'withdrawals') {
                $transType = ($type === 'deposits') ? 'Deposit' : 'Withdraw';
                $data = DB::table('nobs_transactions')
                    ->where('comp_id', $compId)
                    ->where('name_of_transaction', $transType)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->cursor();

                foreach ($data as $row) {
                    fputcsv($file, [$row->created_at, $row->account_number, $row->det_rep_name_of_transaction, $row->amount, $row->name_of_transaction, $row->agentname]);
                }
            } elseif ($type === 'customers') {
                $data = DB::table('nobs_registration')
                    ->where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->cursor();
                foreach ($data as $row) {
                    fputcsv($file, [$row->created_at, $row->account_number, "{$row->first_name} {$row->surname}", $row->phone_number, $row->gender, $row->user]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
