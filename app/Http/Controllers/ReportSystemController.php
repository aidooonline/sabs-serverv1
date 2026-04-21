<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSystemController extends Controller
{
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // --- 1. CUSTOMER VITALITY ---
            $totalCustomers = DB::table('nobs_registration')->where('comp_id', $compId)->count();
            $newRegs = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $customerList = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'DESC')
                ->limit(50)->get();

            // Safe Mapping for Business Value (Avoids SQL Join / Missing Column Errors)
            foreach ($customerList as $customer) {
                // Safely fetch savings
                $savings = DB::table('nobs_user_account_numbers')
                    ->where('comp_id', $compId)
                    ->where('account_number', $customer->account_number)
                    ->first();
                $customer->savings_total = $savings ? round($savings->balance, 2) : 0;

                // Safely fetch debt (using 'amount' from loan_applications, since 'amount_paid' is not a column)
                $customer->debt_total = round(DB::table('loan_applications')
                    ->where('comp_id', $compId)
                    ->where('customer_id', $customer->id)
                    ->where('status', 'active')
                    ->sum('amount'), 2);
            }

            // --- 2. CASH FLOW (Deposits & Withdrawals) ---
            $depositsQuery = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $withdrawalsQuery = DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Withdraw')
                ->whereBetween('created_at', [$startDate, $endDate]);

            $totalDepositAmt = $depositsQuery->sum('amount');
            $totalWithdrawAmt = $withdrawalsQuery->sum('amount');

            // --- 3. LOAN PORTFOLIO HEALTH ---
            $loansQuery = DB::table('loan_applications')
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

            $totalDisbursed = $loansQuery->sum('amount');
            $ldRatio = 0;
            if ($totalDepositAmt > 0) {
                $ldRatio = round(($totalDisbursed / $totalDepositAmt) * 100, 2);
            }

            return response()->json([
                'success' => true,
                'summary' => [
                    'analysis' => [
                        'liquidity' => round($totalDepositAmt - $totalWithdrawAmt, 2),
                        'loan_to_deposit_ratio' => $ldRatio
                    ],
                    'customers' => [
                        'total_customers' => $totalCustomers,
                        'new_registrations' => $newRegs,
                        'list' => $customerList
                    ],
                    'deposits' => [
                        'total_amount' => round($totalDepositAmt, 2),
                        'total_count' => $depositsQuery->count(),
                        'list' => $depositsQuery->orderBy('created_at', 'DESC')->limit(50)->get()
                    ],
                    'withdrawals' => [
                        'total_amount' => round($totalWithdrawAmt, 2),
                        'total_count' => $withdrawalsQuery->count(),
                        'list' => $withdrawalsQuery->orderBy('created_at', 'DESC')->limit(50)->get()
                    ],
                    'loans' => [
                        'total_disbursed' => round($totalDisbursed, 2),
                        'interest_collected' => round($interestCollected, 2),
                        'fees_collected' => round($feesCollected, 2),
                        'list' => $loansQuery->orderBy('created_at', 'DESC')->limit(50)->get()
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Final Logic Error: ' . $e->getMessage()], 500);
        }
    }

    public function saveSnapshot(Request $request)
    {
        DB::beginTransaction();
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $liveDataResponse = $this->getLiveReport($request);
            $liveData = $liveDataResponse->getData()->summary;

            DB::table('report_snapshots')->updateOrInsert(
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
