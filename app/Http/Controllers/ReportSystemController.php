<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSystemController extends Controller
{
    /**
     * Advanced Live Report: High-Performance BI Engine
     * Scalability: Uses indexed subqueries to prevent N+1 performance bottlenecks.
     * Security: Strict comp_id scoping on every data point to prevent leakage.
     */
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // --- 1. CUSTOMER VITALITY (High-Performance Single Query) ---
            $totalCustomers = DB::table('nobs_registration')->where('comp_id', $compId)->count();
            $newRegs = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // Scalable Select: The DB handles the math for 50 records in microseconds
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
                    // Scalar subqueries are optimized by MySQL for small result sets (limit 50)
                    DB::raw("(SELECT COALESCE(balance, 0) FROM nobs_user_account_numbers WHERE account_number = nobs_registration.account_number AND comp_id = $compId LIMIT 1) as savings_total"),
                    DB::raw("(SELECT COALESCE(SUM(amount), 0) FROM loan_applications WHERE customer_id = nobs_registration.id AND comp_id = $compId AND status = 'active') as debt_total")
                )
                ->where('nobs_registration.comp_id', $compId)
                ->whereBetween('nobs_registration.created_at', [$startDate, $endDate])
                ->orderBy('nobs_registration.created_at', 'DESC')
                ->limit(50)->get();

            // --- 2. CASH FLOW (Aggregated for Speed) ---
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

            // --- 3. LOAN PORTFOLIO (Aggregated for Speed) ---
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
            $ldRatio = ($totalDepositAmt > 0) ? round(($totalDisbursed / $totalDepositAmt) * 100, 2) : 0;

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
            // Log for admin debugging, return clean error to user
            \Log::error("BI Report Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System error generating business insights.'], 500);
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
            return response()->json(['success' => true, 'message' => "Archive for $month/$year created successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Snapshot failed.'], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        // Safe streaming export for memory efficiency
        $type = $request->query('type', 'deposits');
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        $compId = auth()->user()->comp_id;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $fileName = "SABS_{$type}_Report_{$month}_{$year}.csv";
        
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
