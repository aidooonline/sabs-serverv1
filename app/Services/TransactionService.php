<?php

namespace App\Services;

use App\AccountsTransactions;
use App\UserAccountNumbers;
use App\CompanyInfo;
use App\Accounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Process a deposit transaction into a user's account.
     * Replicates the logic from ApiUsersController@deposittransaction.
     *
     * @param string $accountNumber
     * @param float $amount
     * @param string $transactionType e.g., 'Deposit', 'Loan Disbursement', 'Commission Payout'
     * @param string|null $narrative Custom description
     * @param array $options Additional options (firstname, surname, phonenumber)
     * @return array Result containing status and new balance
     */
    public function deposit($accountNumber, $amount, $transactionType = 'Deposit', $narrative = null, $options = [])
    {
        $user = Auth::user();
        $compId = $user->comp_id;

        // 1. Fetch Account Info
        $accountInfo = UserAccountNumbers::where('account_number', $accountNumber)
            ->where('comp_id', $compId)
            ->first();

        if (!$accountInfo) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        // 2. Fetch Customer Info (if not provided in options)
        $customerName = 'Unknown Customer';
        $phoneNumber = $options['phonenumber'] ?? '';

        if (!isset($options['firstname'])) {
            $customer = Accounts::where('account_number', $accountInfo->primary_account_number)
                ->where('comp_id', $compId)
                ->first();
            if ($customer) {
                $customerName = $customer->first_name . ' ' . $customer->middle_name . ' ' . $customer->surname;
                $phoneNumber = $customer->phone_number;
            }
        } else {
            $customerName = ($options['firstname'] ?? '') . ' ' . ($options['middlename'] ?? '') . ' ' . ($options['surname'] ?? '');
        }

        // 3. Calculate Balance (The legacy way: summing all history)
        // Note: This matches line 2868 of ApiUsersController.php
        $totaldeposits = AccountsTransactions::where('account_number', $accountNumber)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', $compId)->sum('amount');
        $totalcommission = AccountsTransactions::where('account_number', $accountNumber)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', $compId)->sum('amount');
        $totalwithdrawals = AccountsTransactions::where('account_number', $accountNumber)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', $compId)->sum('amount');
        $totalrefunds = AccountsTransactions::where('account_number', $accountNumber)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', $compId)->sum('amount');

        $currentBalance = ROUND($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);
        $newBalance = ROUND($currentBalance + $amount, 2);

        // 4. Create Transaction Record
        $transaction = new AccountsTransactions();
        $transaction->__id__ = Str::random(30);
        $transaction->account_number = $accountNumber;
        $transaction->account_type = $accountInfo->account_type;
        $transaction->created_at = date("Y-m-d H:i:s");
        $transaction->transaction_id = Str::random(8);
        $transaction->phone_number = $phoneNumber;
        $transaction->det_rep_name_of_transaction = $narrative ?: $customerName;
        $transaction->amount = $amount;
        $transaction->agentname = $user->name;
        $transaction->name_of_transaction = $transactionType; // 'Deposit', 'Loan Disbursement', etc.
        $transaction->users = $user->created_by_user;
        $transaction->is_shown = 1;
        $transaction->is_loan = ($transactionType === 'Loan Disbursement' ? 1 : 0);
        $transaction->row_version = 2;
        $transaction->comp_id = $compId;
        $transaction->balance = $newBalance;

        if ($transaction->save()) {
            // 5. Update Account Table Balance
            UserAccountNumbers::where('account_number', $accountNumber)
                ->where('comp_id', $compId)
                ->update(['balance' => $newBalance]);

            // 6. Optional: SMS Notification (simplified call)
            // In a real scenario, we'd trigger an SMS helper here. 
            // For now, we return success so the controller can handle the response.

            return [
                'success' => true,
                'balance' => $newBalance,
                'transaction_id' => $transaction->transaction_id,
                'message' => 'Transaction successful.'
            ];
        }

        return ['success' => false, 'message' => 'Failed to save transaction.'];
    }
}
