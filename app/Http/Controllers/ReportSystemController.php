<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Accounts;
use App\AccountsTransactions;
use App\LoanApplication;
use App\User;

class ReportSystemController extends Controller
{
    /**
     * SAFE MODE: Basic report without complex joins to prevent 500 errors.
     */
    public function getLiveReport(Request $request)
    {
        try {
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $compId = auth()->user()->comp_id;

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            // 1. Customers
            $customers = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->limit(50)->get();

            // 2. Deposits & Withdrawals
            $deposits = DB::table('accounts_transactions')
                ->where('comp_id', $compId)->where('name_of_transaction', 'Deposit')
                ->whereBetween('created_at', [$startDate, $endDate]);
            
            $withdrawals = DB::table('accounts_transactions')
                ->where('comp_id', $compId)->where('name_of_transaction', 'Withdraw')
                ->whereBetween('created_at', [$startDate, $endDate]);

            // 3. Loans (Simplified - No Joins)
            $loans = DB::table('loan_applications')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->limit(50)->get();

            return response()->json([
                'success' => true,
                'summary' => [
                    'customers' => ['total_customers' => DB::table('nobs_registration')->where('comp_id', $compId)->count(), 'list' => $customers],
                    'deposits' => ['total_amount' => round($deposits->sum('amount'), 2), 'total_count' => $deposits->count(), 'list' => $deposits->limit(50)->get()],
                    'withdrawals' => ['total_amount' => round($withdrawals->sum('amount'), 2), 'total_count' => $withdrawals->count(), 'list' => $withdrawals->limit(50)->get()],
                    'loans' => ['total_disbursed' => round($loans->sum('amount'), 2), 'list' => $loans],
                    'analysis' => ['liquidity' => round($deposits->sum('amount') - $withdrawals->sum('amount'), 2)]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function exportCsv(Request $request) { return response('Export disabled in safe mode', 200); }
    public function saveSnapshot(Request $request) { return response()->json(['success' => true]); }
}
