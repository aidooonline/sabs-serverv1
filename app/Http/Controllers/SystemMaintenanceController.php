<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CompanyInfo;

class SystemMaintenanceController extends Controller
{
    /**
     * Reset the Loan System Data.
     * This action is IRREVERSIBLE. It wipes all loan-related data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetLoanSystem(Request $request)
    {
        // 1. Strict Security Check
        if (!auth()->user()->hasRole('Owner')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only the Owner can reset the system.'
            ], 403);
        }

        // Get targets from request (default to all if not specified, or empty?)
        // Let's expect an array: ['loans', 'commissions', 'ledger', 'pool']
        $targets = $request->input('targets', []);

        if (empty($targets)) {
             return response()->json([
                'success' => false,
                'message' => 'No reset targets selected.'
            ], 400);
        }

        try {
            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Group 1: Loan Data (Applications, Schedules, Defaults, Requirements)
            if (in_array('loans', $targets)) {
                DB::table('loan_applications')->truncate();
                DB::table('loan_repayment_schedules')->truncate();
                DB::table('loan_default_logs')->truncate();
                DB::table('loan_application_requirements')->truncate();
            }

            // Group 2: Commissions (Agent Commissions, Payouts)
            if (in_array('commissions', $targets)) {
                DB::table('agent_commissions')->truncate();
                DB::table('commission_payouts')->truncate();
            }

            // Group 3: Ledger (Nobs Transactions)
            if (in_array('ledger', $targets)) {
                DB::table('nobs_transactions')->whereIn('name_of_transaction', [
                    'Loan Disbursement', 
                    'Loan Repayment'
                ])->delete();
            }

            // Group 4: Pool & Capital (Central Loan Account, Capital Transactions)
            if (in_array('pool', $targets)) {
                DB::table('central_loan_accounts')->update(['balance' => 0]);
                DB::table('fund_transfers')->truncate();
                DB::table('capital_account_transactions')->truncate();
                DB::table('capital_accounts')->update(['balance' => 0]);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selected system data has been successfully reset.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Reset failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fix accounts with negative balances by creating balancing deposits.
     */
    public function fixNegativeBalances(Request $request)
    {
        if (!auth()->user()->hasRole('Owner')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $compId = auth()->user()->comp_id;
            $negativeAccounts = DB::table('nobs_user_account_numbers')
                ->where('comp_id', $compId)
                ->where('balance', '<', 0)
                ->get();

            $count = 0;
            foreach ($negativeAccounts as $account) {
                $adjustmentAmount = abs($account->balance);
                
                // Create balancing transaction
                DB::table('nobs_transactions')->insert([
                    '__id__' => \Str::random(30),
                    'account_number' => $account->account_number,
                    'account_type' => $account->account_type,
                    'amount' => $adjustmentAmount,
                    'name_of_transaction' => 'Admin Adjustment',
                    'det_rep_name_of_transaction' => 'System Correction',
                    'agentname' => 'System',
                    'users' => 'admin',
                    'is_shown' => 1,
                    'row_version' => 2,
                    'comp_id' => $compId,
                    'transaction_id' => \Str::random(8),
                    'created_at' => now(),
                    'balance' => 0.00,
                    'description' => 'System-generated deposit to correct negative balance.'
                ]);

                // Update the account balance
                DB::table('nobs_user_account_numbers')
                    ->where('id', $account->id)
                    ->update([
                        'balance' => 0.00,
                        'last_transaction_date' => now()
                    ]);
                
                $count++;
            }

            return response()->json([
                'success' => true, 
                'message' => "Successfully corrected $count accounts with negative balances."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Correction failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
