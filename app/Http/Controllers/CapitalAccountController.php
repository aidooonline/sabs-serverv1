<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CapitalAccount;
use App\CentralLoanAccount;
use App\FundTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CapitalAccountController extends Controller
{
    /**
     * List all capital accounts (sources).
     */
    public function index()
    {
        $accounts = CapitalAccount::where('is_active', 1)->get();
        return response()->json(['success' => true, 'data' => $accounts], 200);
    }

    /**
     * Create a new capital account.
     */
    public function store(Request $request)
    {
        // DEBUG: Verify we reached here
        dump("CapitalAccountController@store REACHED");

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'balance' => 'numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $account = CapitalAccount::create($request->all());

        return response()->json(['success' => true, 'data' => $account, 'message' => 'Capital Account created successfully'], 201);
    }

    /**
     * Get the Central Loan Pool balance.
     * (Assuming ID 1 is the main pool for now).
     */
    public function getPoolBalance()
    {
        $pool = CentralLoanAccount::first();
        if (!$pool) {
            // Auto-create if missing (failsafe)
            $pool = CentralLoanAccount::create(['name' => 'Main Loan Pool', 'balance' => 0]);
        }
        return response()->json(['success' => true, 'data' => $pool], 200);
    }

    /**
     * Transfer funds from a Capital Account to the Loan Pool.
     */
    public function transferToPool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_account_id' => 'required|exists:capital_accounts,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'created_by' => 'required|integer' // User ID
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $amount = $request->amount;
        $fromAccountId = $request->from_account_id;
        // Default to the first pool for now
        $pool = CentralLoanAccount::first();
        if (!$pool) {
             return response()->json(['success' => false, 'message' => 'Loan Pool not initialized.'], 500);
        }

        DB::beginTransaction();

        try {
            // 1. Lock and Get Source Account
            $source = CapitalAccount::lockForUpdate()->find($fromAccountId);
            
            // Optional: Check if source has enough "balance" (if we are tracking source balance as an asset)
            // For now, we assume we can add funds to the source and then move them, OR just move them.
            // Let's implement: Adding funds to pool DECREASES source? Or is Source just a record of origin?
            // "Credit that source account... then enter how much taken to be added to loan account"
            // Implementation: We assume the user has ALREADY credited the CapitalAccount (via a separate deposit logic not shown here, or manual edit).
            // So we DEBIT the CapitalAccount here.
            
            if ($source->balance < $amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Insufficient funds in Capital Account.'], 400);
            }

            $source->balance -= $amount;
            $source->save();

            // 2. Credit the Pool
            $pool->balance += $amount;
            $pool->save();

            // 3. Log the Transfer
            $transfer = FundTransfer::create([
                'from_account_id' => $fromAccountId,
                'to_account_id' => $pool->id,
                'amount' => $amount,
                'date' => now(),
                'description' => $request->description ?? 'Transfer to Loan Pool',
                'created_by' => $request->created_by
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Funds transferred successfully',
                'pool_balance' => $pool->balance,
                'source_balance' => $source->balance
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Transfer failed: ' . $e->getMessage()], 500);
        }
    }
}
