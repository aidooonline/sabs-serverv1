<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\AccountsTransactions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiUsersController;

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

            // 5. Credit Customer's Account via ApiUsersController (Deep Integration)
            $apiUsersController = new ApiUsersController();
            $depositRequest = new Request();
            $depositRequest->replace([
                'accountnumber'   => $destinationAccount,
                'customerdeposit' => $amountToDisburse,
                'phonenumber'     => $application->customer->phone_number,
                'firstname'       => $application->customer->first_name,
                'middlename'      => $application->customer->middle_name,
                'surname'         => $application->customer->surname,
            ]);

            // Manually set the user on the request for the controller context
            $depositRequest->setUserResolver(function () use ($request) {
                return $request->user();
            });

            $depositResponse = $apiUsersController->deposittransaction($depositRequest);
            $depositData = json_decode($depositResponse->getContent(), true);

            // The deposittransaction returns a JSON response. We need to check if it was successful.
            // A simple check could be for the existence of the 'balance' key. A more robust check is better.
            if ($depositResponse->getStatusCode() == 200 && isset($depositData['balance'])) {
                // If the deposit was successful, we can commit the transaction.
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Loan Disbursed Successfully']);
            } else {
                // If the deposit failed, we must roll back our own transaction.
                DB::rollBack();
                // Pass on the error message from the deposit transaction if available
                $errorMessage = $depositData['message'] ?? 'Failed to post deposit transaction in legacy system.';
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }

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
