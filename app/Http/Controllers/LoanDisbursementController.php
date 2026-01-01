<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\AccountsTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanDisbursementController extends Controller
{
    public function disburse(Request $request, $id)
    {
        $application = LoanApplication::with(['loan_product', 'customer'])->find($id);

        if (!$application) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        if ($application->status !== 'approved') return response()->json(['success' => false, 'message' => 'Loan must be approved first'], 400);

        // Validate Destination Account
        $destinationAccount = $request->input('destination_account_number');
        if (!$destinationAccount) {
             return response()->json(['success' => false, 'message' => 'Destination account is required'], 400);
        }

        $userAccount = \App\UserAccountNumbers::where('account_number', $destinationAccount)
                        ->where('comp_id', $request->user()->comp_id)
                        ->first();

        if (!$userAccount) {
            return response()->json(['success' => false, 'message' => 'Destination account not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Fetch the Central Loan Account
            $centralLoanAccount = \App\CentralLoanAccount::first(); 
            if (!$centralLoanAccount) {
                return response()->json(['success' => false, 'message' => 'Central Loan Account not found'], 500);
            }

            $amountToDisburse = $application->amount;
            if ($application->fee_payment_method == 'deduct_upfront') {
                $amountToDisburse -= $application->total_fees;
            }

            // 1. Check Central Loan Account balance
            if ($centralLoanAccount->balance < $amountToDisburse) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Insufficient funds in Central Loan Account'], 400);
            }

            // 2. Debit Central Loan Account
            $centralLoanAccount->balance -= $amountToDisburse;
            $centralLoanAccount->save();

            // 3. Update Loan Application Status
            $application->status = 'active';
            
            // Set First Repayment Date
            $startDate = Carbon::now()->addMonth(); 
            if ($application->repayment_frequency == 'weekly') {
                $startDate = Carbon::now()->addWeek();
            }
            
            $application->repayment_start_date = $startDate;
            $application->save();

            // 4. Generate Schedule
            $this->generateSchedule($application, $startDate);

            // 5. Credit Customer's Account (Deep Integration)
            // We use 'Deposit' as name_of_transaction so the legacy system sees the money.
            
            $totaldeposits = AccountsTransactions::where('account_number', $destinationAccount)->where('name_of_transaction', 'Deposit')->where('row_version', 2)->where('comp_id', $request->user()->comp_id)->sum('amount');
            $totalcommission = AccountsTransactions::where('account_number', $destinationAccount)->where('name_of_transaction', 'Commission')->where('row_version', 2)->where('comp_id', $request->user()->comp_id)->sum('amount');
            $totalwithdrawals = AccountsTransactions::where('account_number', $destinationAccount)->where('name_of_transaction', 'Withdraw')->where('row_version', 2)->where('comp_id', $request->user()->comp_id)->sum('amount');
            $totalrefunds = AccountsTransactions::where('account_number', $destinationAccount)->where('name_of_transaction', 'Refund')->where('row_version', 2)->where('comp_id', $request->user()->comp_id)->sum('amount');

            // Calculate current balance before this deposit
            $currentBalance = round($totaldeposits - $totalrefunds - $totalwithdrawals - $totalcommission, 3);
            $newBalance = $currentBalance + $amountToDisburse;

            AccountsTransactions::create([
                'account_number' => $destinationAccount,
                'account_type' => $userAccount->account_type, 
                'amount' => $amountToDisburse,
                'det_rep_name_of_transaction' => 'Loan Disbursement',
                'name_of_transaction' => 'Deposit', // Critical for legacy balance calculation
                'transaction_id' => 'LN' . time(),
                'users' => $application->customer->user, 
                'agentname' => $request->user()->name,
                'is_loan' => 1, // Mark as loan related
                'balance' => $newBalance,
                'row_version' => 2,
                'comp_id' => $request->user()->comp_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update UserAccountNumbers
            $userAccount->balance = $newBalance;
            $userAccount->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Loan Disbursed Successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function generateSchedule($application, $startDate)
    {
        $duration = $application->duration;
        $totalRepayment = $application->total_repayment;
        
        // Simple Equal Installments
        $principalPerInstallment = $application->amount / ($duration > 0 ? $duration : 1);
        $interestPerInstallment = $application->total_interest / ($duration > 0 ? $duration : 1);
        $feesPerInstallment = 0; 

        $frequency = $application->repayment_frequency; // monthly, weekly

        for ($i = 0; $i < $duration; $i++) {
            $dueDate = $startDate->copy();
            
            if ($frequency == 'monthly') {
                $dueDate->addMonths($i);
            } elseif ($frequency == 'weekly') {
                $dueDate->addWeeks($i);
            } else {
                $dueDate->addDays($i * 30); // Fallback
            }

            LoanRepaymentSchedule::create([
                'loan_application_id' => $application->id,
                'installment_number' => $i + 1,
                'due_date' => $dueDate,
                'principal_due' => $principalPerInstallment,
                'interest_due' => $interestPerInstallment,
                'fees_due' => $feesPerInstallment,
                'total_due' => $principalPerInstallment + $interestPerInstallment + $feesPerInstallment,
                'status' => 'pending'
            ]);
        }
    }
}
