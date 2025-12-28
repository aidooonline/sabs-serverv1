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
            $loan = LoanApplication::with('customer')->lockForUpdate()->find($loanId);
            
            if (!$loan || !$loan->customer) {
                throw new \Exception("Loan or Customer not found.");
            }

            // 1. Create Transaction Record in `nobs_transactions` (The Ledger)
            $accountNumber = $loan->customer->account_number;
            $accountType = $loan->customer->account_types; // Assuming account_types is the correct field from Accounts model

            $randomCode = \Str::random(8);
            $myid = \Str::random(30);
            $mydatey = date("Y-m-d H:i:s");

            $transaction = new AccountsTransactions();
            $transaction->__id__ = $myid;
            $transaction->account_number = $accountNumber;
            $transaction->account_type = $accountType;
            $transaction->created_at = $mydatey;
            $transaction->transaction_id = $randomCode;
            $transaction->det_rep_name_of_transaction = "Loan Repayment - App #" . $loanId;
            $transaction->amount = $amount;
            $transaction->agentname = $agentName;
            $transaction->name_of_transaction = 'Loan Repayment';
            $transaction->users = $user->created_by_user; 
            $transaction->is_shown = 1;
            $transaction->is_loan = 1;
            $transaction->row_version = 2;
            $transaction->comp_id = $compId;
            
            $totalRepaid = LoanRepaymentSchedule::where('loan_application_id', $loanId)->sum('total_paid');
            $newTotalRepaid = $totalRepaid + $amount;
            $remainingDebt = $loan->total_repayment - $newTotalRepaid;
            
            $transaction->balance = $remainingDebt; 
            $transaction->save();


            // 2. Distribute Payment to Schedules (Waterfall) and Update Accounts
            $remainingPayment = $amount;
            $principalToPool = 0;
            $interestToRevenue = 0;
            $feesToRevenue = 0;
            
            // Get unpaid/partial schedules ordered by due date
            $schedules = LoanRepaymentSchedule::where('loan_application_id', $loanId)
                        ->where('status', '!=', 'paid')
                        ->orderBy('due_date', 'asc')
                        ->get();

            foreach ($schedules as $schedule) {
                if ($remainingPayment <= 0) break;

                $totalDueOnSchedule = $schedule->total_due - ($schedule->principal_paid + $schedule->interest_paid + $schedule->fees_paid);
                if ($totalDueOnSchedule <= 0) {
                    $schedule->status = 'paid';
                    $schedule->save();
                    continue;
                }

                $paymentAppliedToSchedule = min($remainingPayment, $totalDueOnSchedule);
                $remainingPayment -= $paymentAppliedToSchedule;
                
                // Distribute payment for this schedule (Fees -> Interest -> Principal)
                // Fees first
                $feesRemaining = $schedule->fees_due - $schedule->fees_paid;
                if ($feesRemaining > 0) {
                    $payForFees = min($paymentAppliedToSchedule, $feesRemaining);
                    $schedule->fees_paid += $payForFees;
                    $paymentAppliedToSchedule -= $payForFees;
                    $feesToRevenue += $payForFees;
                }

                // Interest next
                $interestRemaining = $schedule->interest_due - $schedule->interest_paid;
                if ($interestRemaining > 0 && $paymentAppliedToSchedule > 0) {
                    $payForInterest = min($paymentAppliedToSchedule, $interestRemaining);
                    $schedule->interest_paid += $payForInterest;
                    $paymentAppliedToSchedule -= $payForInterest;
                    $interestToRevenue += $payForInterest;
                }

                // Principal last
                $principalRemaining = $schedule->principal_due - $schedule->principal_paid;
                if ($principalRemaining > 0 && $paymentAppliedToSchedule > 0) {
                    $payForPrincipal = min($paymentAppliedToSchedule, $principalRemaining);
                    $schedule->principal_paid += $payForPrincipal;
                    $paymentAppliedToSchedule -= $payForPrincipal;
                    $principalToPool += $payForPrincipal;
                }

                $schedule->total_paid = $schedule->principal_paid + $schedule->interest_paid + $schedule->fees_paid;

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

            // 4. Update Central Loan Account and Company Cash (Revenue)
            if ($principalToPool > 0) {
                $centralLoanAccount = \App\CentralLoanAccount::first(); // Assuming one central account
                if ($centralLoanAccount) {
                    $centralLoanAccount->balance += $principalToPool;
                    $centralLoanAccount->save();
                }
            }

            $totalRevenueFromPayment = $interestToRevenue + $feesToRevenue;
            if ($totalRevenueFromPayment > 0) {
                $companyInfo = \App\CompanyInfo::first(); // Assuming one company info record
                if ($companyInfo) {
                    $companyInfo->amount_in_cash += $totalRevenueFromPayment;
                    $companyInfo->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repayment processed successfully.',
                'data' => [
                    'transaction_id' => $randomCode,
                    'new_remaining_debt' => $remainingDebt
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error processing repayment: ' . $e->getMessage()], 500);
        }
    }
}