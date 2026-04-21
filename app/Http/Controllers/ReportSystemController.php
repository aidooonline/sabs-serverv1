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

            return response()->json([
                'success' => true,
                'summary' => [
                    'analysis' => [
                        'liquidity' => round($totalDepositAmt - $totalWithdrawAmt, 2),
                        'loan_to_deposit_ratio' => 0
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
                        'total_disbursed' => 0, 
                        'interest_collected' => 0, 
                        'fees_collected' => 0, 
                        'list' => []
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cash Flow Error: ' . $e->getMessage()], 500);
        }
    }

    public function saveSnapshot(Request $request) { return response()->json(['success' => true]); }
    public function exportCsv(Request $request) { return response()->json(['success' => true]); }
}
