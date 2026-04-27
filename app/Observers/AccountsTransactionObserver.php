<?php

namespace App\Observers;

use App\AccountsTransactions;
use App\AgentPouchLedger;
use App\TreasuryAccount;
use App\TreasuryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AccountsTransactionObserver
{
    /**
     * Handle the AccountsTransactions "created" event.
     * This acts as the "Logical Bridge" to update Treasury Wallets automatically.
     *
     * @param  \App\AccountsTransactions  $transaction
     * @return void
     */
    public function created(AccountsTransactions $transaction)
    {
        try {
            // 1. Identify the Performer
            $user = Auth::user();
            if (!$user) {
                // System cron or automated task
                return;
            }

            $amount = $transaction->amount;
            $type = $transaction->name_of_transaction; // 'Deposit', 'Withdraw', 'Loan Repayment', 'Refund'
            $compId = $transaction->comp_id;
            
            // LOGIC CONTEXT: Identify if this is a reversal/adjustment based on description
            $desc = strtolower($transaction->description ?? '');
            $isReversal = (strpos($desc, 'reversal') !== false || strpos($desc, 'refund') !== false || strpos($desc, 'adjustment') !== false);

            // 2. SMART ROUTING LOGIC
            // If Agent: Cash goes to their Pouch (Debt to office).
            // If Manager/Admin: Cash goes directly to the Office Safe.
            
            $isAgent = ($user->type == 'Agents' || $user->type == 'Agent' || $user->hasRole('Agent'));
            
            $sourceType = '';
            $destType = '';
            $sourceId = null;
            $destId = null;
            $txType = '';

            if ($isAgent) {
                // --- AGENT ROUTING ---
                $pouch = AgentPouchLedger::firstOrCreate(
                    ['agent_id' => $user->id, 'comp_id' => $compId]
                );

                if ($type == 'Deposit' || $type == 'Loan Repayment') {
                    $pouch->increment('current_balance', $amount);
                    $sourceType = 'customer_deposit';
                    $destType = 'agent_pouch';
                    $destId = $pouch->id;
                    $txType = $isReversal ? 'reversal' : 'deposit';
                } elseif ($type == 'Withdraw' || $type == 'Refund') {
                    $pouch->decrement('current_balance', $amount);
                    $sourceType = 'agent_pouch';
                    $sourceId = $pouch->id;
                    $destType = 'customer_withdrawal';
                    $txType = $isReversal ? 'reversal' : 'withdrawal';
                } else {
                    return; // Ignore other types
                }
            } else {
                // --- MANAGEMENT ROUTING (Direct to Safe) ---
                $safe = TreasuryAccount::where('comp_id', $compId)
                    ->where('account_type', 'safe')
                    ->where('is_active', 1)
                    ->first();

                if (!$safe) {
                    Log::warning("Treasury: No active safe found for Company $compId during Management transaction.");
                    return;
                }

                if ($type == 'Deposit' || $type == 'Loan Repayment') {
                    $safe->increment('balance', $amount);
                    $sourceType = 'customer_deposit';
                    $destType = 'treasury_account';
                    $destId = $safe->id;
                    $txType = $isReversal ? 'reversal' : 'deposit';
                } elseif ($type == 'Withdraw' || $type == 'Refund') {
                    $safe->decrement('balance', $amount);
                    $sourceType = 'treasury_account';
                    $sourceId = $safe->id;
                    $destType = 'customer_withdrawal';
                    $txType = $isReversal ? 'reversal' : 'withdrawal';
                } else {
                    return;
                }
            }

            // 3. Record the movement in the Treasury Ledger
            TreasuryTransaction::create([
                'comp_id' => $compId,
                'transaction_code' => 'BRIDGE-' . Str::upper(Str::random(12)),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'destination_type' => $destType,
                'destination_id' => $destId,
                'amount' => $amount,
                'transaction_type' => $txType,
                'description' => "Bridge Entry: $type by " . $user->name . ($isReversal ? " (REVERSAL)" : ""),
                'related_legacy_tx_id' => $transaction->__id__,
                'status' => 'completed',
                'performed_by' => $user->id
            ]);

        } catch (\Throwable $e) {
            // STRICT ZERO-CRASH POLICY
            Log::error('Treasury Bridge Error: ' . $e->getMessage());
        }
    }
}
