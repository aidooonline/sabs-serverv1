<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserAccountNumbers;
use App\AccountsTransactions;
use App\CompanyInfo;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    /**
     * Fix accounts with negative balances by creating a correction deposit.
     */
    public function fixNegativeBalances(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            
            // Find accounts with negative balance
            $negativeAccounts = UserAccountNumbers::where('comp_id', $compId)
                                ->where('balance', '<', 0)
                                ->get();

            $count = 0;
            $totalFixed = 0;

            DB::beginTransaction();

            foreach ($negativeAccounts as $acc) {
                $amountNeeded = abs($acc->balance);
                
                if ($amountNeeded > 0) {
                    // 1. Update Account Balance
                    $acc->balance = 0;
                    $acc->save();

                    // 2. Create Transaction Record
                    $trans = new AccountsTransactions();
                    $trans->account_number = $acc->account_number;
                    $trans->amount = $amountNeeded;
                    $trans->name_of_transaction = 'Correction Deposit';
                    $trans->det_rep_name_of_transaction = 'System Balance Correction';
                    $trans->users = auth()->user()->id;
                    $trans->comp_id = $compId;
                    $trans->created_at = Carbon::now();
                    $trans->updated_at = Carbon::now();
                    $trans->row_version = 2; // Standard transaction version
                    $trans->save();

                    $count++;
                    $totalFixed += $amountNeeded;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => "Fixed $count accounts. Total adjusted: $totalFixed."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reset selected data modules (The "Red Button").
     */
    public function resetLoans(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $targets = $request->input('targets', []);
        $compId = auth()->user()->comp_id;
        $actionsTaken = [];

        DB::beginTransaction();
        try {
            if (in_array('loans', $targets)) {
                // Delete schedules first (FK constraint)
                DB::table('loan_repayment_schedules')->where('comp_id', $compId)->delete();
                DB::table('loan_applications')->where('comp_id', $compId)->delete();
                // Also clear legacy tables
                DB::table('nobs_micro_loan_request')->where('comp_id', $compId)->delete();
                $actionsTaken[] = 'Loans';
            }

            if (in_array('commissions', $targets)) {
                DB::table('agent_commissions')->where('comp_id', $compId)->delete();
                DB::table('commission_payouts')->where('comp_id', $compId)->delete();
                $actionsTaken[] = 'Commissions';
            }

            if (in_array('ledger', $targets)) {
                // Delete loan-related transactions only
                DB::table('nobs_transactions')
                    ->where('comp_id', $compId)
                    ->whereIn('name_of_transaction', ['Loan Disbursement', 'Loan Repayment', 'Commission Payout'])
                    ->delete();
                $actionsTaken[] = 'Ledger (Loans)';
            }

            if (in_array('pool', $targets)) {
                DB::table('central_loan_accounts')->where('comp_id', $compId)->update(['balance' => 0, 'total_deposited' => 0]);
                DB::table('capital_accounts')->where('comp_id', $compId)->delete();
                $actionsTaken[] = 'Pool & Capital';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reset successful: ' . implode(', ', $actionsTaken)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isAdmin()
    {
        $user = auth()->user();
        return in_array($user->type, ['owner', 'super admin', 'God Admin', 'Owner', 'Admin']);
    }
}
