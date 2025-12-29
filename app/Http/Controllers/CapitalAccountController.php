<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CapitalAccount;
use App\CentralLoanAccount;
use App\FundTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\CapitalAccountTransaction;

class CapitalAccountController extends Controller
{
    /**
     * List all capital accounts (sources).
     */
    public function index()
    {
        $accounts = CapitalAccount::where('is_active', 1)->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $accounts], 200);
    }

    /**
     * Create a new capital account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'balance' => 'numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $account = CapitalAccount::create($request->all());

        // Log initial balance as a transaction if > 0
        if ($request->balance > 0) {
            CapitalAccountTransaction::create([
                'capital_account_id' => $account->id,
                'amount' => $request->balance,
                'type' => 'initial_balance',
                'description' => 'Initial Balance',
                'date' => now(),
                // 'created_by' => auth()->id() // If auth is available
            ]);
        }

        return response()->json(['success' => true, 'data' => $account, 'message' => 'Capital Account created successfully'], 201);
    }

    /**
     * Add funds to a capital account (e.g. new loan from bank).
     */
    public function addFunds(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:capital_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $account = CapitalAccount::lockForUpdate()->find($request->account_id);
            
            // 1. Create Transaction Record
            CapitalAccountTransaction::create([
                'capital_account_id' => $account->id,
                'amount' => $request->amount,
                'type' => 'deposit',
                'description' => $request->description ?? 'Add Funds',
                'date' => now(),
                'created_by' => $request->created_by // Optional: Pass user ID from frontend
            ]);

            // 2. Update Account Balance
            $account->balance += $request->amount;
            $account->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funds added successfully',
                'new_balance' => $account->balance
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get transaction history for a capital account.
     */
    public function getHistory(Request $request, $id)
    {
        $transactions = CapitalAccountTransaction::where('capital_account_id', $id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $transactions], 200);
    }

    /**
     * Update a specific transaction.
     */
    public function updateTransaction(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $transaction = CapitalAccountTransaction::lockForUpdate()->find($id);
            if (!$transaction) {
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            $account = CapitalAccount::lockForUpdate()->find($transaction->capital_account_id);
            
            // Calculate difference
            $oldAmount = $transaction->amount;
            $newAmount = $request->amount;
            $difference = $newAmount - $oldAmount;

            // Update Transaction
            $transaction->amount = $newAmount;
            if ($request->has('description')) {
                $transaction->description = $request->description;
            }
            $transaction->type = 'edit'; // Mark as edited
            $transaction->save();

            // Update Account Balance
            $account->balance += $difference;
            $account->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Transaction updated',
                'new_balance' => $account->balance
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Directly adjust the balance of a capital account.
     */
    public function adjustBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:capital_accounts,id',
            'new_balance' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $account = CapitalAccount::lockForUpdate()->find($request->account_id);
            
            $currentBalance = $account->balance;
            $newBalance = $request->new_balance;
            $difference = $newBalance - $currentBalance;

            if ($difference == 0) {
                return response()->json(['success' => true, 'message' => 'No change in balance', 'new_balance' => $currentBalance], 200);
            }

            // Create "Correction" Transaction
            CapitalAccountTransaction::create([
                'capital_account_id' => $account->id,
                'amount' => $difference,
                'type' => 'correction',
                'description' => 'Manual Balance Adjustment',
                'date' => now(),
                'created_by' => $request->created_by
            ]);

            // Update Balance
            $account->balance = $newBalance;
            $account->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Balance adjusted successfully',
                'new_balance' => $account->balance
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
        // The frontend expects a 'balance' property, not a nested 'data' object.
        return response()->json(['success' => true, 'balance' => $pool->balance], 200);
    }

    /**
     * Get history of transfers to the loan pool.
     */
    public function getPoolTransferHistory()
    {
        $history = FundTransfer::join('capital_accounts', 'fund_transfers.from_account_id', '=', 'capital_accounts.id')
            ->select('fund_transfers.*', 'capital_accounts.name as source_name')
            ->orderBy('fund_transfers.date', 'desc')
            ->orderBy('fund_transfers.id', 'desc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $history], 200);
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
