<?php

namespace App\Http\Controllers;

use App\AgentPouchLedger;
use App\TreasuryAccount;
use App\TreasuryTransaction;
use App\BusinessExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TreasuryController extends Controller
{
    /**
     * Reconcile Agent Pouch with Physical Cash
     * Moves money from Agent Pouch to Office Safe
     */
    public function closeAgentDay(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'physical_cash_received' => 'required|numeric|min:0',
            'safe_id' => 'required|exists:treasury_accounts,id',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $compId = Auth::user()->comp_id;
            $agentId = $request->agent_id;
            $cashReceived = $request->physical_cash_received;
            
            // 1. Get Agent Pouch
            $pouch = AgentPouchLedger::where('agent_id', $agentId)
                ->where('comp_id', $compId)
                ->first();

            if (!$pouch) {
                return response()->json(['success' => false, 'message' => 'Agent pouch not found.'], 404);
            }

            $digitalBalance = $pouch->current_balance;
            $shortfall = $digitalBalance - $cashReceived;

            // 2. Move Cash to Safe
            $safe = TreasuryAccount::where('id', $request->safe_id)
                ->where('comp_id', $compId)
                ->first();

            if (!$safe) {
                return response()->json(['success' => false, 'message' => 'Office Safe not found.'], 404);
            }

            $safe->balance += $cashReceived;
            $safe->save();

            // 3. Update Pouch Balance
            // The pouch balance now becomes the shortfall (debt) that carries over
            $pouch->current_balance = $shortfall;
            $pouch->last_closing_date = now();
            $pouch->save();

            // 4. Record Main Transfer Transaction (Pouch -> Safe)
            TreasuryTransaction::create([
                'comp_id' => $compId,
                'transaction_code' => 'CLS-' . Str::upper(Str::random(12)),
                'source_type' => 'agent_pouch',
                'source_id' => $pouch->id,
                'destination_type' => 'treasury_account',
                'destination_id' => $safe->id,
                'amount' => $cashReceived,
                'transaction_type' => 'transfer',
                'description' => "EOD Closing: Cash received from agent. " . ($request->notes ?? ''),
                'status' => 'completed',
                'performed_by' => Auth::user()->id
            ]);

            // 5. If there was a shortfall, record it for auditing
            if ($shortfall != 0) {
                TreasuryTransaction::create([
                    'comp_id' => $compId,
                    'transaction_code' => 'SHT-' . Str::upper(Str::random(12)),
                    'source_type' => 'agent_pouch',
                    'source_id' => $pouch->id,
                    'destination_type' => 'internal_debt',
                    'amount' => abs($shortfall),
                    'transaction_type' => $shortfall > 0 ? 'closing_shortfall' : 'closing_excess',
                    'description' => $shortfall > 0 ? "Shortfall identified during closing." : "Excess cash identified during closing.",
                    'status' => 'completed',
                    'performed_by' => Auth::user()->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Agent day closed successfully.',
                'summary' => [
                    'digital_pouch_balance' => $digitalBalance,
                    'cash_received' => $cashReceived,
                    'carryover_debt' => $shortfall,
                    'new_safe_balance' => $safe->balance
                ]
            ]);
        });
    }

    /**
     * Get all Treasury Wallets and Pouch Balances
     */
    public function getTreasuryOverview()
    {
        $compId = Auth::user()->comp_id;

        $wallets = TreasuryAccount::where('comp_id', $compId)->get();
        $agentPouches = DB::table('agent_pouch_ledger')
            ->join('users', 'agent_pouch_ledger.agent_id', '=', 'users.id')
            ->where('agent_pouch_ledger.comp_id', $compId)
            ->select('users.name as agent_name', 'agent_pouch_ledger.*')
            ->get();

        return response()->json([
            'wallets' => $wallets,
            'agent_pouches' => $agentPouches
        ]);
    }

    /**
     * Initialize a Treasury Wallet (Safe or Bank)
     * Used for setting Opening Balances
     */
    public function initializeWallet(Request $request)
    {
        $request->validate([
            'account_type' => 'required|string',
            'account_name' => 'required|string',
            'opening_balance' => 'required|numeric|min:0'
        ]);

        $compId = Auth::user()->comp_id;

        $wallet = TreasuryAccount::create([
            'comp_id' => $compId,
            'account_type' => $request->account_type,
            'account_name' => $request->account_name,
            'balance' => $request->opening_balance,
            'created_by' => Auth::user()->id
        ]);

        // Record opening transaction
        TreasuryTransaction::create([
            'comp_id' => $compId,
            'transaction_code' => 'OPN-' . Str::upper(Str::random(12)),
            'source_type' => 'opening_balance',
            'destination_type' => 'treasury_account',
            'destination_id' => $wallet->id,
            'amount' => $request->opening_balance,
            'transaction_type' => 'deposit',
            'description' => "Initial wallet setup for " . $request->account_name,
            'status' => 'completed',
            'performed_by' => Auth::user()->id
        ]);

        return response()->json(['success' => true, 'wallet' => $wallet]);
    }

    /**
     * Record Business Expense
     */
    public function recordExpense(Request $request)
    {
        $request->validate([
            'treasury_account_id' => 'required|exists:treasury_accounts,id',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'expense_date' => 'required|date'
        ]);

        return DB::transaction(function () use ($request) {
            $compId = Auth::user()->comp_id;

            $wallet = TreasuryAccount::where('id', $request->treasury_account_id)
                ->where('comp_id', $compId)
                ->first();

            if ($wallet->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient funds in wallet.'], 400);
            }

            // 1. Deduct from wallet
            $wallet->balance -= $request->amount;
            $wallet->save();

            // 2. Create Expense Record
            $expense = \App\BusinessExpense::create([
                'comp_id' => $compId,
                'treasury_account_id' => $wallet->id,
                'category' => $request->category,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date,
                'recorded_by' => Auth::user()->id
            ]);

            // 3. Record in Ledger
            TreasuryTransaction::create([
                'comp_id' => $compId,
                'transaction_code' => 'EXP-' . Str::upper(Str::random(12)),
                'source_type' => 'treasury_account',
                'source_id' => $wallet->id,
                'destination_type' => 'business_expense',
                'amount' => $request->amount,
                'transaction_type' => 'withdrawal',
                'description' => "Expense: " . $request->category . " - " . ($request->description ?? ''),
                'status' => 'completed',
                'performed_by' => Auth::user()->id
            ]);

            return response()->json(['success' => true, 'expense' => $expense, 'new_balance' => $wallet->balance]);
        });
    }

    /**
     * Transfer Funds between internal wallets (Safe <-> Bank)
     */
    public function transferFunds(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:treasury_accounts,id',
            'to_account_id' => 'required|exists:treasury_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $compId = Auth::user()->comp_id;

            $from = TreasuryAccount::where('id', $request->from_account_id)->where('comp_id', $compId)->first();
            $to = TreasuryAccount::where('id', $request->to_account_id)->where('comp_id', $compId)->first();

            if ($from->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient funds in source wallet.'], 400);
            }

            $from->balance -= $request->amount;
            $to->balance += $request->amount;

            $from->save();
            $to->save();

            TreasuryTransaction::create([
                'comp_id' => $compId,
                'transaction_code' => 'TRF-' . Str::upper(Str::random(12)),
                'source_type' => 'treasury_account',
                'source_id' => $from->id,
                'destination_type' => 'treasury_account',
                'destination_id' => $to->id,
                'amount' => $request->amount,
                'transaction_type' => 'transfer',
                'description' => "Internal Transfer: " . ($request->notes ?? ''),
                'status' => 'completed',
                'performed_by' => Auth::user()->id
            ]);

            return response()->json(['success' => true, 'from_balance' => $from->balance, 'to_balance' => $to->balance]);
        });
    }

    /**
     * Executive Summary for AI Assistant & Management
     */
    public function getExecutiveSummary()
    {
        $compId = Auth::user()->comp_id;
        $today = date('Y-m-d');

        // 1. Core Wallet Balances
        $safeBalance = TreasuryAccount::where('comp_id', $compId)->where('account_type', 'safe')->sum('balance');
        $bankBalance = TreasuryAccount::where('comp_id', $compId)->where('account_type', 'bank')->sum('balance');
        
        // 2. Agent Field Debt
        $totalFieldDebt = AgentPouchLedger::where('comp_id', $compId)->where('current_balance', '>', 0)->sum('current_balance');
        
        // 3. Today's Expenses
        $todayExpenses = \App\BusinessExpense::where('comp_id', $compId)->where('expense_date', $today)->sum('amount');

        // 4. Calculate Net Position
        $netLiquidCash = $safeBalance + $bankBalance;
        $totalCompanyValue = $netLiquidCash + $totalFieldDebt;

        return response()->json([
            'date' => $today,
            'wallets' => [
                'office_safe' => round($safeBalance, 2),
                'bank_accounts' => round($bankBalance, 2),
                'liquid_total' => round($netLiquidCash, 2)
            ],
            'field_assets' => [
                'outstanding_agent_debt' => round($totalFieldDebt, 2)
            ],
            'daily_stats' => [
                'expenses_today' => round($todayExpenses, 2)
            ],
            'total_position' => round($totalCompanyValue, 2)
        ]);
    }
}
