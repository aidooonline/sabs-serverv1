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

            // --- 1. CUSTOMER VITALITY (Safe version) ---
            $totalCustomers = DB::table('nobs_registration')->where('comp_id', $compId)->count();
            $newRegs = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $customerList = DB::table('nobs_registration')
                ->where('comp_id', $compId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'DESC')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'summary' => [
                    'analysis' => ['liquidity' => 0, 'loan_to_deposit_ratio' => 0],
                    'customers' => [
                        'total_customers' => $totalCustomers,
                        'new_registrations' => $newRegs,
                        'list' => $customerList
                    ],
                    'deposits' => ['total_amount' => 0, 'total_count' => 0, 'list' => []],
                    'withdrawals' => ['total_amount' => 0, 'total_count' => 0, 'list' => []],
                    'loans' => ['total_disbursed' => 0, 'interest_collected' => 0, 'fees_collected' => 0, 'list' => []]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Logic Error: ' . $e->getMessage()], 500);
        }
    }

    public function saveSnapshot(Request $request) { return response()->json(['success' => true]); }
    public function exportCsv(Request $request) { return response()->json(['success' => true]); }
}
