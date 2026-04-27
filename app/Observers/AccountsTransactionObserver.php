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
            // 1. Identify the Agent (The user currently performing the action)
            $user = Auth::user();
            
            // If it's a system-generated transaction (e.g. cron) or no user is logged in,
            // we don't update a pouch.
            if (!$user) {
                return;
            }

            // Only process for Agents
            $isAgent = ($user->type == 'Agents' || $user->type == 'Agent' || $user->hasRole('Agent'));
            if (!$isAgent) {
                return;
            }

            $amount = $transaction->amount;
            $type = $transaction->name_of_transaction; // 'Deposit', 'Withdraw'
            $compId = $transaction->comp_id;

            // 2. Find or Create the Agent's Digital Pouch
            // This pouch tracks the cash the agent physically holds.
            $pouch = AgentPouchLedger::firstOrCreate(
                ['agent_id' => $user->id, 'comp_id' => $compId]
            );

            // 3. Logic for updating Pouch Balance based on Transaction Type
            $sourceType = '';
            $destType = '';
            $txType = '';

            if ($type == 'Deposit') {
                // Customer gives Cash to Agent -> Agent Pouch Balance Increases
                $pouch->current_balance += $amount;
                $sourceType = 'customer_deposit';
                $destType = 'agent_pouch';
                $txType = 'deposit';
            } elseif ($type == 'Withdraw') {
                // Agent gives Cash to Customer -> Agent Pouch Balance Decreases
                $pouch->current_balance -= $amount;
                $sourceType = 'agent_pouch';
                $destType = 'customer_withdrawal';
                $txType = 'withdrawal';
            } else {
                // Other transaction types (like internal adjustments) are ignored for now.
                return;
            }

            $pouch->save();

            // 4. Record the movement in the Treasury Ledger
            TreasuryTransaction::create([
                'comp_id' => $compId,
                'transaction_code' => 'BRIDGE-' . Str::upper(Str::random(12)),
                'source_type' => $sourceType,
                'source_id' => ($sourceType == 'agent_pouch' ? $pouch->id : null),
                'destination_type' => $destType,
                'destination_id' => ($destType == 'agent_pouch' ? $pouch->id : null),
                'amount' => $amount,
                'transaction_type' => $txType,
                'description' => "Bridge Entry: " . $type . " by agent " . $user->name,
                'related_legacy_tx_id' => $transaction->__id__,
                'status' => 'completed',
                'performed_by' => $user->id
            ]);

        } catch (\Throwable $e) {
            // STRICT ZERO-CRASH POLICY
            // We log the error so we can fix it, but we MUST NOT stop the legacy app.
            Log::error('Treasury Bridge Error for Tx ' . ($transaction->__id__ ?? 'NEW') . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
