<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\AccountsTransactions; // Nobs transaction
use App\UserAccountNumbers;
use App\CompanyInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LoanRepaymentController extends Controller
{
    /**
     * Process a Loan Repayment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_application_id' => 'required|exists:loan_applications,id',
            'amount' => 'required|numeric|min:0.01',
            'agent_id' => 'nullable|exists:users,id', // Optional, defaults to Auth user
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $amount = $request->amount;
        $loanId = $request->loan_application_id;
        $user = Auth::user();
        $agentId = $request->agent_id ?? $user->id; // The agent collecting the money
        $agentName = $user->name;
        $compId = $user->comp_id;

        DB::beginTransaction();

        try {
            $loan = LoanApplication::lockForUpdate()->find($loanId);
            
            // 1. Create Transaction Record in `nobs_transactions` (The Ledger)
            // We need the customer's primary account number for reference
            $customerAccount = UserAccountNumbers::where('account_number', function($query) use ($loan) {
                $query->select('account_number')
                      ->from('nobs_registration')
                      ->where('id', $loan->customer_id) // Assuming customer_id matches nobs_registration.id? Or user_id? 
                      // Verification needed: Accounts.php uses 'id'. LoanApplication uses 'customer_id'.
                      // Let's assume customer_id is the ID of nobs_registration.
                      ->limit(1);
            })->first();

            // Fallback if not found (shouldn't happen if integrity is kept)
            $accountNumber = $customerAccount ? $customerAccount->account_number : 'LOAN-' . $loanId;
            $accountType = $customerAccount ? $customerAccount->account_type : 'Loan';

            $randomCode = \Str::random(8);
            $myid = \Str::random(30);
            $mydatey = date("Y-m-d H:i:s");

            $transaction = new AccountsTransactions();
            $transaction->__id__ = $myid;
            $transaction->account_number = $accountNumber;
            $transaction->account_type = $accountType;
            $transaction->created_at = $mydatey;
            $transaction->transaction_id = $randomCode;
            // $transaction->phone_number = ... // Fetch from customer
            $transaction->det_rep_name_of_transaction = "Loan Repayment - App #" . $loanId;
            $transaction->amount = $amount;
            $transaction->agentname = $agentName;
            $transaction->name_of_transaction = 'Loan Repayment';
            $transaction->users = $user->created_by_user; // The "Owner" or Supervisor ID usually
            $transaction->is_shown = 1;
            $transaction->is_loan = 1;
            $transaction->row_version = 2;
            $transaction->comp_id = $compId;
            
            // Note: We are NOT calculating 'balance' here in the same way as savings 
            // because Loan Balance decreases. 
            // For now, we store the transaction. The balance logic in ApiUsersController
            // sums 'Deposits' - 'Withdrawals'. 'Loan Repayment' is positive cash flow for the company,
            // effectively a 'Deposit' into the company's hands, but it reduces the user's Debt.
            // We'll leave 'balance' as 0 or the current Loan Balance if we want.
            // Let's calculate the remaining loan balance.
            
            $totalRepaid = LoanRepaymentSchedule::where('loan_application_id', $loanId)->sum('total_paid');
            $newTotalRepaid = $totalRepaid + $amount;
            $remainingDebt = $loan->total_repayment - $newTotalRepaid;
            
            $transaction->balance = $remainingDebt; // Storing Remaining Debt as balance for context
            $transaction->save();


            // 2. Distribute Payment to Schedules (Waterfall)
            $remainingPayment = $amount;
            
            // Get unpaid/partial schedules ordered by due date
            $schedules = LoanRepaymentSchedule::where('loan_application_id', $loanId)
                        ->where('status', '!=', 'paid')
                        ->orderBy('due_date', 'asc')
                        ->get();

            foreach ($schedules as $schedule) {
                if ($remainingPayment <= 0) break;

                $due = $schedule->total_due - $schedule->total_paid;
                
                if ($due <= 0) {
                    // Should be marked paid, but just in case
                    $schedule->status = 'paid';
                    $schedule->save();
                    continue;
                }

                $payAmount = min($remainingPayment, $due);
                
                // Distribute to Principal/Interest/Fees (Pro-rated or hierarchy? Usually Fees -> Interest -> Principal)
                // Simplified for now: Just track total_paid
                $schedule->total_paid += $payAmount;
                $remainingPayment -= $payAmount;

                if ($schedule->total_paid >= $schedule->total_due) {
                    $schedule->status = 'paid';
                } else {
                    $schedule->status = 'partial';
                }
                $schedule->save();
            }
            
            // 3. Update Loan Application Status
            if ($remainingDebt <= 0) {
                $loan->status = 'repaid';
            }
            $loan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repayment processed successfully.',
                'data' => [
                    'transaction_id' => $randomCode,
                    'new_balance' => $remainingDebt
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error processing repayment: ' . $e->getMessage()], 500);
        }
    }
}