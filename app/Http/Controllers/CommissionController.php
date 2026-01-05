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
        $user = Auth::user();
        $canManage = $user->hasRole(['Admin', 'Owner', 'super admin', 'Manager']);

        // Base query for earned commissions
        $query = AgentCommission::where('status', 'earned')
            ->select('agent_id', DB::raw('SUM(amount) as total_unpaid'))
            ->with('agent:id,name,phone,email') // Eager load agent details
            ->groupBy('agent_id');

        // If not an admin/manager, only show their own commissions
        if (!$canManage) {
            $query->where('agent_id', $user->id);
        }

        $summary = $query->get();

        // Also get global settings
        $percentage = SystemSetting::where('key', 'agent_commission_percent')->value('value') ?? 1.00;

        return response()->json([
            'success' => true,
            'data' => $summary,
            'settings' => ['percentage' => $percentage],
            'can_manage' => $canManage
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
            'destination_account_number' => 'required|string|exists:nobs_user_account_numbers,account_number'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
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
            // Find the customer details associated with the destination account for the deposit narrative
            $userAccount = UserAccountNumbers::where('account_number', $destAccount)->first();
            $customerInfo = DB::table('nobs_registration')->where('account_number', $userAccount->primary_account_number)->first();

            if (!$customerInfo) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Could not find the account holder for the destination account.'], 404);
            }

            // 1. Credit Agent's Account via ApiUsersController (Deep Integration)
            $apiUsersController = new ApiUsersController();
            $depositRequest = new Request();
            $depositRequest->replace([
                'accountnumber'   => $destAccount,
                'customerdeposit' => $payoutAmount,
                'phonenumber'     => $customerInfo->phone_number,
                'firstname'       => $customerInfo->first_name,
                'middlename'      => $customerInfo->middle_name,
                'surname'         => $customerInfo->surname,
            ]);
            $depositRequest->setUserResolver(fn() => Auth::user());

            $depositResponse = $apiUsersController->deposittransaction($depositRequest);
            $depositData = json_decode($depositResponse->getContent(), true);

            // Check if the legacy deposit transaction was successful
            if ($depositResponse->getStatusCode() != 200 || !isset($depositData['balance'])) {
                DB::rollBack();
                $errorMessage = $depositData['message'] ?? 'Failed to post commission deposit in legacy system.';
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            
            // If the deposit was successful, continue with logging the payout and updating commission statuses.
            $randomCode = $depositData['transaction_id'] ?? \Str::random(8); // Use transaction ID from response if available

            // 2. Log the Payout
            $payout = CommissionPayout::create([
                'agent_id' => $agentId,
                'amount' => $payoutAmount,
                'destination_account_number' => $destAccount,
                'transaction_ref' => $randomCode,
                'performed_by_user_id' => Auth::id()
            ]);

            // 3. Mark specific commission records as 'paid' (FIFO)
            $commissionsToPay = AgentCommission::where('agent_id', $agentId)
                ->where('status', 'earned')
                ->orderBy('created_at', 'asc')
                ->get();
            
            $remainingToMarkAsPaid = $payoutAmount;

            foreach ($commissionsToPay as $comm) {
                if ($remainingToMarkAsPaid <= 0) break;

                if ($comm->amount <= $remainingToMarkAsPaid) {
                    $comm->status = 'paid';
                    $comm->payout_id = $payout->id;
                    $comm->save();
                    $remainingToMarkAsPaid -= $comm->amount;
                } else {
                    // This is a partial payment on a commission record.
                    // We split the record into a paid part and a remaining earned part.
                    AgentCommission::create([
                        'agent_id' => $comm->agent_id,
                        'loan_application_id' => $comm->loan_application_id,
                        'transaction_id' => $comm->transaction_id,
                        'amount' => $comm->amount - $remainingToMarkAsPaid,
                        'calculation_base' => $comm->calculation_base,
                        'percentage' => $comm->percentage,
                        'status' => 'earned'
                    ]);

                    $comm->amount = $remainingToMarkAsPaid;
                    $comm->status = 'paid';
                    $comm->payout_id = $payout->id;
                    $comm->save();
                    
                    $remainingToMarkAsPaid = 0;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Commission disbursed successfully.', 
                'new_balance' => $depositData['balance']
            ], 200);

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
