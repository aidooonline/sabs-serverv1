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

        DB::beginTransaction();
        try {
            // Fetch the Central Loan Account (assuming there's one main account for now)
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
            
            // Set First Repayment Date (e.g., 1 month from now)
            // Adjust logic based on frequency later (weekly = 1 week)
            $startDate = Carbon::now()->addMonth(); 
            if ($application->repayment_frequency == 'weekly') {
                $startDate = Carbon::now()->addWeek();
            }
            
            $application->repayment_start_date = $startDate;
            $application->save();

            // 4. Generate Schedule
            $this->generateSchedule($application, $startDate);

            // 5. Credit Customer's Account
            AccountsTransactions::create([
                'account_number' => $application->customer->account_number,
                'account_type' => 'Savings', 
                'amount' => $amountToDisburse,
                'det_rep_name_of_transaction' => 'Loan Disbursement',
                'name_of_transaction' => 'Deposit', // This represents a credit to the customer's account
                'transaction_id' => 'LN' . time(),
                'users' => $application->customer->user, 
                'agentname' => $request->user() ? $request->user()->name : 'System',
                'is_loan' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

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
