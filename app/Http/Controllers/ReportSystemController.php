<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSystemController extends Controller
{
    /**
     * Advanced Live Report: High-Performance BI Engine
     */
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            if ($startDate->isFuture()) {
                return response()->json([
                    'success' => true,
                    'summary' => $this->emptySummary(),
                    'message' => 'No data available for future periods.'
                ]);
            }

            // --- 1. SYSTEM LIQUIDITY & POSITION (The "Main Point") ---
            // A. Actual Cash In Hand (All time deposits - all time withdrawals)
            $totalAllTimeDeposits = DB::table('nobs_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Deposit')->sum('amount');
            $totalAllTimeWithdrawals = DB::table('nobs_transactions')->where('comp_id', $compId)->where('name_of_transaction', 'Withdraw')->sum('amount');
            $actualCashInHand = $totalAllTimeDeposits - $totalAllTimeWithdrawals;

            // B. Total Savings Liability (What we owe customers)
            $totalSavingsLiability = DB::table('nobs_user_account_numbers')->where('comp_id', $compId)->sum('balance');

            // C. Net System Position (Surplus/Deficit)
            $netSystemPosition = $actualCashInHand - $totalSavingsLiability;

            // --- 2. CUSTOMER VITALITY ---
            $totalCustomers = DB::table('nobs_registration')->where('comp_id', $compId)->count();
            $newRegs = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // FIXED: Explicit Correlation Aliases to prevent global sum bug
            $customerList = DB::table('nobs_registration')
                ->select(
                    'nobs_registration.id',
                    'nobs_registration.account_number',
                    'nobs_registration.first_name',
                    'nobs_registration.surname',
                    'nobs_registration.phone_number',
                    'nobs_registration.gender',
                    'nobs_registration.user',
                    'nobs_registration.created_at',
                    DB::raw("(SELECT COALESCE(SUM(balance), 0) FROM nobs_user_account_numbers WHERE nobs_user_account_numbers.phone_number = nobs_registration.phone_number AND nobs_user_account_numbers.comp_id = $compId) as savings_total"),
                    DB::raw("(SELECT COALESCE(SUM(amount), 0) FROM loan_applications WHERE loan_applications.customer_id = nobs_registration.id AND loan_applications.comp_id = $compId AND loan_applications.status = 'active') as debt_total")
                )
                ->where('nobs_registration.comp_id', $compId)
                ->whereBetween('nobs_registration.created_at', [$startDate, $endDate])
                ->orderBy('nobs_registration.created_at', 'DESC')
                ->limit(50)->get();

            // --- 3. PERIOD CASH FLOW ---
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

            // --- 4. LOAN PORTFOLIO ---
            $loansQuery = DB::table('loan_applications')
                ->leftJoin('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                ->select(
                    'loan_applications.*',
                    DB::raw("CONCAT(nobs_registration.first_name, ' ', nobs_registration.surname) as customer_name"),
                    'nobs_registration.account_number as customer_account_number',
                    DB::raw("(SELECT COALESCE(SUM(principal_paid + interest_paid + fees_paid), 0) FROM loan_repayment_schedules WHERE loan_application_id = loan_applications.id) as amount_paid"),
                    DB::raw("(loan_applications.total_repayment - (SELECT COALESCE(SUM(principal_paid + interest_paid + fees_paid), 0) FROM loan_repayment_schedules WHERE loan_application_id = loan_applications.id)) as outstanding_balance")
                )
                ->where('loan_applications.comp_id', $compId)
                ->whereBetween('loan_applications.created_at', [$startDate, $endDate]);

            $totalDisbursed = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $ldRatio = ($totalDepositAmt > 0) ? round(($totalDisbursed / $totalDepositAmt) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'summary' => [
                    'analysis' => [
                        'liquidity' => round($totalDepositAmt - $totalWithdrawAmt, 2),
                        'loan_to_deposit_ratio' => $ldRatio,
                        'net_system_position' => round($netSystemPosition, 2),
                        'total_savings_liability' => round($totalSavingsLiability, 2)
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
                        'interest_collected' => round(DB::table('loan_repayment_schedules')->where('comp_id', $compId)->whereBetween('updated_at', [$startDate, $endDate])->sum('interest_paid'), 2),
                        'fees_collected' => round(DB::table('loan_repayment_schedules')->where('comp_id', $compId)->whereBetween('updated_at', [$startDate, $endDate])->sum('fees_paid'), 2),
                        'list' => $loansQuery->orderBy('loan_applications.created_at', 'DESC')->limit(50)->get()
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error("BI Report Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function emptySummary() {
        return [
            'analysis' => ['liquidity' => 0, 'loan_to_deposit_ratio' => 0, 'net_system_position' => 0, 'total_savings_liability' => 0],
            'customers' => ['total_customers' => 0, 'new_registrations' => 0, 'list' => []],
            'deposits' => ['total_amount' => 0, 'total_count' => 0, 'list' => []],
            'withdrawals' => ['total_amount' => 0, 'total_count' => 0, 'list' => []],
            'loans' => ['total_disbursed' => 0, 'interest_collected' => 0, 'fees_collected' => 0, 'list' => []]
        ];
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
            return response()->json(['success' => true, 'message' => "Archive created successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Snapshot failed.'], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'deposits');
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        $compId = $request->query('compId', auth()->user()->comp_id);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $timestamp = date('Ymd_His');
        $fileName = "SABS_{$type}_Report_{$timestamp}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ($type === 'customers') 
            ? ['Reg Date', 'Account', 'Name', 'Phone', 'Gender', 'Agent']
            : ['Date', 'Account', 'Name', 'Amount', 'Type', 'Agent'];

        $callback = function() use($type, $compId, $startDate, $endDate, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            if ($type === 'deposits' || $type === 'withdrawals') {
                $transType = ($type === 'deposits') ? 'Deposit' : 'Withdraw';
                DB::table('nobs_transactions')
                    ->where('comp_id', $compId)
                    ->where('name_of_transaction', $transType)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'ASC')
                    ->chunk(500, function($rows) use($file) {
                        foreach ($rows as $row) {
                            fputcsv($file, [$row->created_at, $row->account_number, $row->det_rep_name_of_transaction, $row->amount, $row->name_of_transaction, $row->agentname]);
                        }
                    });
            } elseif ($type === 'customers') {
                DB::table('nobs_registration')
                    ->where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'ASC')
                    ->chunk(500, function($rows) use($file) {
                        foreach ($rows as $row) {
                            fputcsv($file, [$row->created_at, $row->account_number, "{$row->first_name} {$row->surname}", $row->phone_number, $row->gender, $row->user]);
                        }
                    });
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
