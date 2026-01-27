<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Accounts;
use App\UserAccountNumbers;
use App\AccountsTransactions;
use App\SusuCycles;
use App\SavingsAccounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerTransactionHelperController extends Controller
{
    /**
     * Get aggregated data for the Deposit/Withdrawal page.
     * This reduces API chattiness by combining Balance, Account List, and Susu Status into one request.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepositPageData(Request $request)
    {
        // 1. Authorization Check
        $user = Auth::user();
        if (!$this->isAuthorized($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $accountNumber = $request->input('account_number'); // Optional: Specific account to check balance for
        $customerId = $request->input('customer_id'); // Optional: If we want to fetch by ID (not primary in this flow but useful)
        $primaryAccountNumber = $request->input('primary_account_number'); // Used to fetch the list

        $response = [
            'success' => true,
            'accounts' => [],
            'selected_account' => null
        ];

        // 2. Fetch Account List (if primary account number is provided)
        if ($primaryAccountNumber) {
            $response['accounts'] = UserAccountNumbers::select('id', 'account_number', 'account_type', 'balance', 'account_status')
                ->where('comp_id', $user->comp_id)
                ->where('primary_account_number', $primaryAccountNumber)
                ->get();
        }

        // 3. Fetch Specific Account Details (Balance & Susu Status)
        // If no account_number provided, try to use the first one from the list or the primary
        $targetAccount = $accountNumber ?? $primaryAccountNumber;

        if ($targetAccount) {
            // A. Calculate Balance (Using Transaction History for accuracy, mirroring ApiUsersController logic)
            // Note: ApiUsersController::getaccountbalance2 logic
            $totalDeposits = AccountsTransactions::where('account_number', $targetAccount)
                ->where('name_of_transaction', 'Deposit')
                ->where('row_version', 2)
                ->where('comp_id', $user->comp_id)
                ->sum('amount');

            $totalCommissions = AccountsTransactions::where('account_number', $targetAccount)
                ->where('name_of_transaction', 'Commission')
                ->where('row_version', 2)
                ->where('comp_id', $user->comp_id)
                ->sum('amount');

            $totalWithdrawals = AccountsTransactions::where('account_number', $targetAccount)
                ->where('name_of_transaction', 'Withdraw')
                ->where('row_version', 2)
                ->where('comp_id', $user->comp_id)
                ->sum('amount');

            $totalRefunds = AccountsTransactions::where('account_number', $targetAccount)
                ->where('name_of_transaction', 'Refund')
                ->where('row_version', 2)
                ->where('comp_id', $user->comp_id)
                ->sum('amount');

            $balance = round($totalDeposits - $totalRefunds - $totalWithdrawals - $totalCommissions, 2);

            // B. Check Susu Status
            $susuCycle = SusuCycles::where('account_number', $targetAccount)
                ->where('comp_id', $user->comp_id)
                ->where('is_complete', 0) // Get active cycle
                ->first();

            // C. Get Commission/Charges Info (from SavingsAccounts table via Account Type)
            $accountType = UserAccountNumbers::where('account_number', $targetAccount)
                ->where('comp_id', $user->comp_id)
                ->value('account_type');
            
            $commissionInfo = [
                'commission_value' => 0,
                'minimum_balance' => 0,
                'charge_type' => null
            ];

            if ($accountType) {
                $systemAccount = SavingsAccounts::where('account_name', $accountType)
                    ->where('comp_id', $user->comp_id)
                    ->first();
                
                if ($systemAccount) {
                    $commissionInfo['commission_value'] = $systemAccount->withdrawal_commission;
                    $commissionInfo['minimum_balance'] = $systemAccount->minimum_balance ?? 0;
                    $commissionInfo['charge_type'] = $systemAccount->if_commission_charge_type;
                }
            }

            // D. Calculate Available Balance (for withdrawal)
            // Logic mirrored from getaccountbalaceandcharges
            $withdrawalFee = ($commissionInfo['commission_value'] / 100) * $balance; 
            // Note: The original logic calculates commission based on the *balance* available to withdraw? 
            // Actually original logic: $availableamount = calculatePercentage($totalbalance, $commissionvalue);
            // $thebalance = ($totalbalance - $availableamount);
            
            $availableBalance = $balance - $withdrawalFee;


            $response['selected_account'] = [
                'account_number' => $targetAccount,
                'balance' => $balance,
                'susu_status' => $susuCycle ? 'True' : 'False',
                'susu_details' => $susuCycle, // Entire object if needed
                'commission_info' => $commissionInfo,
                'available_balance' => round($availableBalance, 2),
                'withdrawal_fee_estimate' => round($withdrawalFee, 2)
            ];
        }

        return response()->json($response);
    }

    private function isAuthorized($user)
    {
        if (!$user) return false;
        
        $allowedTypes = ['Admin', 'owner', 'super admin', 'God Admin', 'Manager', 'Agents', 'Agent'];
        if (in_array($user->type, $allowedTypes)) return true;
        
        if ($user->hasRole(['Admin', 'Owner', 'super admin', 'Manager', 'Agent'])) return true;

        return false;
    }
}
