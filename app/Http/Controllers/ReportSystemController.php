<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;
use App\CompanyInfo;

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
            
            // Explicitly use api guard for consistency with route definition
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 200);
            }
            $compId = $user->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            if ($startDate->isFuture()) {
                return response()->json([
                    'success' => true,
                    'summary' => $this->emptySummary(),
                    'message' => 'No data available for future periods.'
                ]);
            }

            // --- 1. SYSTEM LIQUIDITY & POSITION (HISTORICAL) ---
            // Calculate liability as of the END of the selected period
            $totalPeriodDeposits = (float)DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Deposit')
                ->where('created_at', '<=', $endDate)
                ->where('amount', '<', 1000000)
                ->sum('amount');
                
            $totalPeriodWithdrawals = (float)DB::table('nobs_transactions')
                ->where('comp_id', $compId)
                ->where('name_of_transaction', 'Withdraw')
                ->where('created_at', '<=', $endDate)
                ->where('amount', '<', 1000000)
                ->sum('amount');
            
            // Historical liability = Total Deposits - Total Withdrawals up to that date
            $totalSavingsLiability = $totalPeriodDeposits - $totalPeriodWithdrawals;
            
            // Cash in hand is more of a "now" metric, but for reports we match the period
            $actualCashInHand = $totalPeriodDeposits - $totalPeriodWithdrawals; 
            
            // Net System Position at that time
            $netSystemPosition = $actualCashInHand - $totalSavingsLiability;

            // --- 2. CUSTOMER VITALITY (HISTORICAL) ---
            $totalCustomers = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->where('created_at', '<=', $endDate)
                ->count();
                
            $newRegs = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
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
                    // FIX: Joined on account_number instead of non-existent phone_number in nobs_user_account_numbers
                    DB::raw("(SELECT COALESCE(SUM(balance), 0) FROM nobs_user_account_numbers WHERE nobs_user_account_numbers.account_number = nobs_registration.account_number AND nobs_user_account_numbers.comp_id = $compId) as savings_total"),
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
                        'total_savings_liability' => round($totalSavingsLiability, 2),
                        '_debug_cash_in_hand' => round($actualCashInHand, 2),
                        '_debug_all_deposits' => round($totalAllTimeDeposits, 2),
                        '_debug_all_withdrawals' => round($totalAllTimeWithdrawals, 2)
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
        } catch (\Throwable $e) {
            // Log for server diagnostics even if file log is tricky
            return response()->json([
                'success' => false, 
                'message' => 'Server Error: ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 200); // Return 200 so Axios doesn't throw generic 500
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

    public function exportCsv(Request $request)
    {
        $compId = $request->query('comp_id');
        $user = auth('api')->user();
        
        if (!$compId && $user) {
            $compId = $user->comp_id;
        }

        if (!$compId) {
            return response('Unauthorized: Missing company context.', 401);
        }

        $type = $request->query('type', 'deposits');
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

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

        // Define Columns based on type
        switch($type) {
            case 'customers':
                $columns = ['Reg Date', 'Account Number', 'Customer Name', 'Phone', 'Gender', 'Agent', 'Total Savings', 'Total Debt'];
                break;
            case 'loans':
                $columns = ['Date', 'Loan ID', 'Customer Name', 'Account Number', 'Granted Amount', 'Total to Repay', 'Total Paid', 'Outstanding Balance', 'Agent'];
                break;
            case 'deposits':
            case 'withdrawals':
            default:
                $columns = ['Date', 'Account Number', 'Customer Name', 'Amount', 'Running Balance', 'Agent Name'];
                break;
        }

        $callback = function() use($type, $compId, $startDate, $endDate, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $totalAmount = 0;
            $totalRepay = 0;
            $totalPaid = 0;
            $totalBalance = 0;
            $totalSavings = 0;
            $totalDebt = 0;

            if ($type === 'deposits' || $type === 'withdrawals') {
                $transType = ($type === 'deposits') ? 'Deposit' : 'Withdraw';
                DB::table('nobs_transactions')
                    ->where('comp_id', $compId)
                    ->where('name_of_transaction', $transType)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'ASC')
                    ->chunk(500, function($rows) use($file, &$totalAmount) {
                        foreach ($rows as $row) {
                            $totalAmount += (float)$row->amount;
                            fputcsv($file, [
                                $row->created_at, 
                                $row->account_number, 
                                $row->det_rep_name_of_transaction, 
                                '="' . number_format($row->amount, 2) . '"', 
                                '="' . number_format($row->balance, 2) . '"', 
                                $row->agentname
                            ]);
                        }
                    });
                fputcsv($file, ['TOTAL', '', '', '="' . number_format($totalAmount, 2) . '"', '', '']);

            } elseif ($type === 'loans') {
                DB::table('loan_applications')
                    ->leftJoin('nobs_registration', 'loan_applications.customer_id', '=', 'nobs_registration.id')
                    ->select(
                        'loan_applications.*',
                        DB::raw("CONCAT(nobs_registration.first_name, ' ', nobs_registration.surname) as customer_name"),
                        'nobs_registration.account_number as customer_account_number',
                        DB::raw("(SELECT COALESCE(SUM(principal_paid + interest_paid + fees_paid), 0) FROM loan_repayment_schedules WHERE loan_application_id = loan_applications.id) as amount_paid")
                    )
                    ->where('loan_applications.comp_id', $compId)
                    ->whereBetween('loan_applications.created_at', [$startDate, $endDate])
                    ->orderBy('loan_applications.created_at', 'ASC')
                    ->chunk(500, function($rows) use($file, &$totalAmount, &$totalRepay, &$totalPaid, &$totalBalance) {
                        foreach ($rows as $row) {
                            $outstanding = $row->total_repayment - $row->amount_paid;
                            $totalAmount += (float)$row->amount;
                            $totalRepay += (float)$row->total_repayment;
                            $totalPaid += (float)$row->amount_paid;
                            $totalBalance += (float)$outstanding;
                            
                            fputcsv($file, [
                                $row->created_at,
                                $row->id,
                                $row->customer_name,
                                $row->customer_account_number,
                                '="' . number_format($row->amount, 2) . '"',
                                '="' . number_format($row->total_repayment, 2) . '"',
                                '="' . number_format($row->amount_paid, 2) . '"',
                                '="' . number_format($outstanding, 2) . '"',
                                $row->created_by
                            ]);
                        }
                    });
                fputcsv($file, ['TOTAL', '', '', '', '="' . number_format($totalAmount, 2) . '"', '="' . number_format($totalRepay, 2) . '"', '="' . number_format($totalPaid, 2) . '"', '="' . number_format($totalBalance, 2) . '"', '']);

            } elseif ($type === 'customers') {
                DB::table('nobs_registration')
                    ->where('comp_id', $compId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'ASC')
                    ->chunk(500, function($rows) use($file, $compId, &$totalSavings, &$totalDebt) {
                        foreach ($rows as $row) {
                            $savings = DB::table('nobs_user_account_numbers')->where('account_number', $row->account_number)->where('comp_id', $compId)->sum('balance');
                            $debt = DB::table('loan_applications')->where('customer_id', $row->id)->where('comp_id', $compId)->where('status', 'active')->sum('amount');
                            
                            $totalSavings += $savings;
                            $totalDebt += $debt;

                            fputcsv($file, [
                                $row->created_at, 
                                $row->account_number, 
                                "{$row->first_name} {$row->surname}", 
                                $row->phone_number, 
                                $row->gender, 
                                $row->user,
                                '="' . number_format($savings, 2) . '"',
                                '="' . number_format($debt, 2) . '"'
                            ]);
                        }
                    });
                fputcsv($file, ['TOTAL', '', '', '', '', '', '="' . number_format($totalSavings, 2) . '"', '="' . number_format($totalDebt, 2) . '"']);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveSnapshot(Request $request)
    {
        DB::beginTransaction();
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 200);
            
            $compId = $user->comp_id;
            
            // Fetch live data to snapshot
            $liveDataResponse = $this->getLiveReport($request);
            $responseArray = $liveDataResponse->getData(true);
            
            if (!$responseArray['success']) {
                throw new \Exception($responseArray['message']);
            }
            
            $liveData = $responseArray['summary'];

            DB::table('report_snapshots')->updateOrInsert(
                ['comp_id' => $compId, 'period_month' => $month, 'period_year' => $year],
                [
                    'total_customers_count' => $liveData['customers']['total_customers'],
                    'new_registrations' => $liveData['customers']['new_registrations'],
                    'total_deposit_amount' => $liveData['deposits']['total_amount'],
                    'total_deposit_count' => $liveData['deposits']['total_count'],
                    'total_withdrawal_amount' => $liveData['withdrawals']['total_amount'],
                    'total_withdrawal_count' => $liveData['withdrawals']['total_count'],
                    'net_cash_flow' => $liveData['analysis']['liquidity'],
                    'total_disbursed_amount' => $liveData['loans']['total_disbursed'],
                    'total_repayments_collected' => round($liveData['loans']['total_disbursed'] * 0, 2), // Placeholder for actual repayment sum if needed
                    'interest_collected' => $liveData['loans']['interest_collected'],
                    'fees_collected' => $liveData['loans']['fees_collected'],
                    'generated_by_user_id' => $user->id,
                    'created_at' => now()
                ]
            );
            DB::commit();
            return response()->json(['success' => true, 'message' => "Archive for $month/$year created successfully."]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Snapshot failed: ' . $e->getMessage()], 200);
        }
    }

    public function listSnapshots(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 200);
            
            $snapshots = DB::table('report_snapshots')
                ->where('comp_id', $user->comp_id)
                ->orderBy('period_year', 'DESC')
                ->orderBy('period_month', 'DESC')
                ->get();
                
            return response()->json(['success' => true, 'snapshots' => $snapshots]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }
    }
}
