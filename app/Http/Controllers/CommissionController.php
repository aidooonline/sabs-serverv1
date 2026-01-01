<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AgentCommission;
use App\CommissionPayout;
use App\AccountsTransactions;
use App\UserAccountNumbers;
use App\CompanyInfo;
use App\SystemSetting;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommissionController extends Controller
{
    /**
     * Get summary of unpaid commissions for all agents.
     */
    public function summary(Request $request)
    {
        if (!Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Manager'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Group by Agent and sum 'earned' status
        $summary = AgentCommission::where('status', 'earned')
            ->select('agent_id', DB::raw('SUM(amount) as total_unpaid'))
            ->with('agent:id,name,phone,email') // Eager load agent details
            ->groupBy('agent_id')
            ->get();

        // Also get global settings
        $percentage = SystemSetting::where('key', 'agent_commission_percent')->value('value') ?? 1.00;

        return response()->json([
            'success' => true,
            'data' => $summary,
            'settings' => ['percentage' => $percentage]
        ], 200);
    }

    /**
     * Get detailed commission history for a specific agent.
     */
    public function history(Request $request, $agentId)
    {
        if (!Auth::user()->hasRole(['Admin', 'Owner', 'super admin', 'Manager']) && Auth::id() != $agentId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $history = AgentCommission::where('agent_id', $agentId)
            ->with('loan.customer') // Show which loan generated the commission
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $history], 200);
    }

    /**
     * Disburse (Payout) commission to an agent's account.
     */
    public function payout(Request $request)
    {
        if (!Auth::user()->hasRole(['Admin', 'Owner', 'super admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'destination_account_number' => 'required|exists:nobs_user_account_numbers,account_number'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $agentId = $request->agent_id;
        $payoutAmount = $request->amount;
        $destAccount = $request->destination_account_number;

        // Verify agent has enough 'earned' balance
        $totalEarned = AgentCommission::where('agent_id', $agentId)->where('status', 'earned')->sum('amount');
        
        if ($payoutAmount > $totalEarned) {
             return response()->json(['success' => false, 'message' => 'Insufficient earned commission balance.'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Create the Payout Record
            $randomCode = \Str::random(8);
            $myid = \Str::random(30);
            
            // 2. Create Transaction (Deposit)
            $accountInfo = UserAccountNumbers::where('account_number', $destAccount)->first();
            
            $transaction = new AccountsTransactions();
            $transaction->__id__ = $myid;
            $transaction->account_number = $destAccount;
            $transaction->account_type = $accountInfo->account_type;
            $transaction->created_at = now();
            $transaction->transaction_id = $randomCode;
            $transaction->det_rep_name_of_transaction = "Commission Payout";
            $transaction->amount = $payoutAmount;
            $transaction->agentname = Auth::user()->name; // Admin performing the payout
            $transaction->name_of_transaction = 'Deposit'; // Treat as deposit so it appears in balance logic
            $transaction->users = Auth::id(); 
            $transaction->is_shown = 1;
            $transaction->is_loan = 0; // Not a loan transaction
            $transaction->row_version = 2;
            $transaction->comp_id = Auth::user()->comp_id;
            
            // Calculate new balance (standard logic)
            // We use the same logic as the legacy system (summing all transactions) to ensure accuracy
            $totaldeposits = AccountsTransactions::where('account_number', $destAccount)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', Auth::user()->comp_id)->sum('amount');
            $totalcommission = AccountsTransactions::where('account_number', $destAccount)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', Auth::user()->comp_id)->sum('amount');
            $totalwithdrawals = AccountsTransactions::where('account_number', $destAccount)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', Auth::user()->comp_id)->sum('amount');
            $totalrefunds = AccountsTransactions::where('account_number', $destAccount)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', Auth::user()->comp_id)->sum('amount');

            $currentBalance = round($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);
            $newBalance = $currentBalance + $payoutAmount;
            
            $transaction->balance = $newBalance;
            $transaction->save();

            // Update Account Balance
            $accountInfo->balance = $newBalance;
            $accountInfo->save();

            // 3. Log the Payout
            $payout = CommissionPayout::create([
                'agent_id' => $agentId,
                'amount' => $payoutAmount,
                'destination_account_number' => $destAccount,
                'transaction_ref' => $randomCode,
                'performed_by_user_id' => Auth::id()
            ]);

            // 4. Mark specific commission records as 'paid' (FIFO - First In First Out)
            // We need to find exactly which records equal the payout amount. 
            // Simplified: We mark records until the sum is reached.
            
            $commissions = AgentCommission::where('agent_id', $agentId)
                ->where('status', 'earned')
                ->orderBy('created_at', 'asc')
                ->get();
            
            $remainingToPay = $payoutAmount;

            foreach ($commissions as $comm) {
                if ($remainingToPay <= 0) break;

                if ($comm->amount <= $remainingToPay) {
                    // Pay in full
                    $comm->status = 'paid';
                    $comm->payout_id = $payout->id;
                    $comm->save();
                    $remainingToPay -= $comm->amount;
                } else {
                    // Partial payment (Split the record)
                    // 1. Create a new "remaining" record
                    AgentCommission::create([
                        'agent_id' => $comm->agent_id,
                        'loan_application_id' => $comm->loan_application_id,
                        'transaction_id' => $comm->transaction_id,
                        'amount' => $comm->amount - $remainingToPay,
                        'calculation_base' => $comm->calculation_base,
                        'percentage' => $comm->percentage,
                        'status' => 'earned' // Still earned
                    ]);

                    // 2. Mark current as paid (with the paid amount)
                    $comm->amount = $remainingToPay;
                    $comm->status = 'paid';
                    $comm->payout_id = $payout->id;
                    $comm->save();
                    
                    $remainingToPay = 0;
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Commission disbursed successfully.', 'new_balance' => $newBalance], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Payout failed: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Update Global Commission Settings.
     */
    public function updateSettings(Request $request) 
    {
        if (!Auth::user()->hasRole(['Admin', 'Owner', 'super admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'percentage' => 'required|numeric|min:0|max:100'
        ]);
        
         if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }
        
        SystemSetting::updateOrCreate(
            ['key' => 'agent_commission_percent'],
            ['value' => $request->percentage, 'description' => 'Percentage of loan repayment given to agent']
        );
        
        return response()->json(['success' => true, 'message' => 'Commission settings updated.'], 200);
    }
}
