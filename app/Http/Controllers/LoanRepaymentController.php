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
        // Add safeguards for null user object (e.g., if token is valid but user deleted)
        $agentId = $request->agent_id ?? ($user ? $user->id : null);
        $agentName = $user ? $user->name : 'System';
        $originalCompId = $user ? $user->comp_id : null;

        DB::beginTransaction();

        try {
            $loan = LoanApplication::with('customer')->lockForUpdate()->find($loanId);
            
            if (!$loan || !$loan->customer) {
                throw new \Exception("Loan or Customer not found.");
            }

            // 1. Create Transaction Record in `nobs_transactions` (The Ledger)
            $accountNumber = $loan->customer->account_number;
            $accountType = $loan->customer->account_types ?? 'default';
            // Use original compId but fallback to customer's comp_id if needed
            $compId = $originalCompId ?? $loan->customer->comp_id;

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
            $transaction->users = $agentId; 
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

            // 5. Calculate and Record Agent Commission
            $commissionPercent = \App\SystemSetting::where('key', 'agent_commission_percent')->value('value') ?? 1.00;
            
            // Determine beneficiary agent:
            // Priority 1: Agent selected in the form (passed as agent_id)
            // Priority 2: Agent assigned to the loan
            // Priority 3: The user making the payment (if they are an agent)
            $beneficiaryAgentId = $request->agent_id;
            if (!$beneficiaryAgentId) {
                $beneficiaryAgentId = $loan->assigned_to_user_id;
            }
            // If still null, check if auth user is agent
            if (!$beneficiaryAgentId && $user && $user->hasRole('Agent')) {
                $beneficiaryAgentId = $user->id;
            }

            if ($beneficiaryAgentId) {
                $commissionAmount = $amount * ($commissionPercent / 100);
                
                \App\AgentCommission::create([
                    'agent_id' => $beneficiaryAgentId,
                    'loan_application_id' => $loan->id,
                    'transaction_id' => $randomCode, // Link to the repayment transaction
                    'amount' => $commissionAmount,
                    'calculation_base' => $amount,
                    'percentage' => $commissionPercent,
                    'status' => 'earned'
                ]);
            }

            // 6. Send SMS Notification if enabled
            $is_sms_enabled = CompanyInfo::where('id', $compId)
                ->where('sms_active', 1)
                ->where('sms_credit', '>', 0)
                ->exists();

            if ($is_sms_enabled && $loan->customer->phone_number) {
                $this->company_sms_transaction('sub', 1);

                $thesmsid = CompanyInfo::where('id', $compId)->value('sms_sender_id');
                $formattedAmount = number_format($amount, 2);
                $formattedBalance = number_format($remainingDebt, 2);
                
                $themessage = "Dear Customer, your loan repayment of GHS {$formattedAmount} was successful. Your new outstanding balance is GHS {$formattedBalance}.";

                try {
                    $this->sendFrogMessage('NYB', 'Populaire123^', $thesmsid, $themessage, $loan->customer->phone_number);
                } catch (\Exception $smsException) {
                    \Log::error("SMS sending failed for loan repayment: " . $smsException->getMessage());
                    // Do not roll back the main transaction if only SMS fails.
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

    /**
     * Generate HTML receipt for a specific loan repayment transaction.
     */
    public function getRepaymentReceipt(Request $request, $loanId, $transactionId)
    {
        $loanApplication = LoanApplication::with('customer', 'loan_product')
                                        ->find($loanId);
        $repaymentTransaction = AccountsTransactions::where('transaction_id', $transactionId)
                                                    ->where('is_loan', 1)
                                                    ->where('name_of_transaction', 'Loan Repayment')
                                                    ->first();

        if (!$loanApplication || !$repaymentTransaction) {
            return response()->json(['success' => false, 'message' => 'Repayment receipt not found'], 404);
        }

        // Fetch company info (assuming first one is the main company)
        $companyInfo = CompanyInfo::first();
        $companyName = $companyInfo ? $companyInfo->name : 'SABS Lending';
        $companyPhone = $companyInfo ? $companyInfo->phone : 'N/A';
        $companyAddress = $companyInfo ? $companyInfo->billing_address : 'N/A';

        // Basic HTML structure for thermal printer
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Repayment Receipt</title>
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <style>
                body {
                    font-family: 'monospace', 'Courier New', monospace;
                    font-size: 10px;
                    width: 80mm; /* Typical thermal printer width */
                    margin: 0;
                    padding: 0;
                }
                .container {
                    padding: 5mm;
                }
                .header, .footer {
                    text-align: center;
                    margin-bottom: 5mm;
                }
                .header h3, .header h4 {
                    margin: 1mm 0;
                }
                .details table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 5mm;
                }
                .details th, .details td {
                    text-align: left;
                    padding: 1mm 0;
                }
                .details td.right {
                    text-align: right;
                }
                .divider {
                    border-top: 1px dashed black;
                    margin: 2mm 0;
                }
                .amount-due {
                    font-size: 12px;
                    font-weight: bold;
                }
                .strong {
                    font-weight: bold;
                }
                .thanks {
                    margin-top: 5mm;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class=\"container\">
                <div class=\"header\">
                    <h3>$companyName</h3>
                    <h4>Loan Repayment Receipt</h4>
                    <p>$companyAddress | Tel: $companyPhone</p>
                    <p>Date: " . Carbon::parse($repaymentTransaction->created_at)->format('Y-m-d H:i:s') . "</p>
                    <p>Transaction ID: <span class=\"strong\">" . $repaymentTransaction->transaction_id . "</span></p>
                </div>

                <div class=\"divider\"></div>

                <div class=\"details\">
                    <table>
                        <tr>
                            <td>Customer:</td>
                            <td class=\"right strong\">" . $loanApplication->customer->first_name . " " . $loanApplication->customer->surname . "</td>
                        </tr>
                        <tr>
                            <td>Account No:</td>
                            <td class=\"right\">" . $loanApplication->customer->account_number . "</td>
                        </tr>
                        <tr>
                            <td>Loan ID:</td>
                            <td class=\"right\">" . $loanApplication->id . "</td>
                        </tr>
                        <tr>
                            <td>Loan Product:</td>
                            <td class=\"right\">" . $loanApplication->loan_product->name . "</td>
                        </tr>
                    </table>
                </div>

                <div class=\"divider\"></div>

                <div class=\"details\">
                    <table>
                        <tr>
                            <td>Repayment Amount:</td>
                            <td class=\"right amount-due\">" . number_format($repaymentTransaction->amount, 2) . "</td>
                        </tr>
                        <tr>
                            <td>New Outstanding:</td>
                            <td class=\"right amount-due\">" . number_format($repaymentTransaction->balance, 2) . "</td>
                        </tr>
                    </table>
                </div>

                <div class=\"divider\"></div>

                <div class=\"footer\">
                    <p>Processed by: " . ($request->user() ? $request->user()->name : 'System') . "</p>
                    <p class=\"thanks\">Thank you for your business!</p>
                </div>
            </div>
        </body>
        </html>
        ";

                return response($html)->header('Content-Type', 'text/html');

            }

        

            public function sendFrogMessage($theusername, $thepass, $thesenderid, $themessage, $thenumbersent)

            {

                $baseUrl = 'https://banqpopulaire.website/nobsimages2/sendfrogmsg.php';

        

                $params = [

                    'theusername' => $theusername,

                    'thepass' => $thepass,

                    'thesenderid' => $thesenderid,

                    'themessage' => $themessage,

                    'thenumbersent' => $thenumbersent,

                ];

        

                // Build the query string

                $queryString = http_build_query($params);

        

                // Create the full URL

                $url = $baseUrl . '?' . $queryString;

        

                // Initialize cURL session

                $ch = curl_init($url);

        

                // Set cURL options

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        

                // Execute cURL session and get the response

                $response = curl_exec($ch);

        

                // Check for cURL errors

                if (curl_errno($ch)) {

                    // Handle the error without throwing an exception to prevent rollbacks

                    \Log::error('cURL Error in sendFrogMessage: ' . curl_error($ch));

                    return response()->json(['status' => 'error', 'message' => curl_error($ch)]);

                }

        

                // Close cURL session

                curl_close($ch);

        

                // Handle the response data accordingly

                return response()->json(['status' => 'success', 'data' => $response]);

            }

        

            public function company_sms_transaction($operation, $value)

            {

                // Check user type

                if (\Auth::user()->type == 'Admin' || \Auth::user()->type == 'owner' || \Auth::user()->type == 'super admin' || \Auth::user()->type == 'Agents') {

                    // Get the current credit value

                    $currentCredit = CompanyInfo::where('id', \Auth::user()->comp_id)->value('sms_credit');

        

                    if ($operation == 'add') {

        

                        // Decrease the credit by 1

                        $newCredit = $currentCredit + $value;

        

                        // Update the database with the new credit value

                        $thereturned = CompanyInfo::where('id', \Auth::user()->comp_id)

                            ->update([

                                'sms_credit' => $newCredit

                            ]);

                        return $thereturned;

                    } else {

                        // Check if there is credit available

                        if ($currentCredit > 0) {

                            // Decrease the credit by 1

                            $newCredit = $currentCredit - $value;

        

                            // Update the database with the new credit value

                            $thereturnedd = CompanyInfo::where('id', \Auth::user()->comp_id)

                                ->update([

                                    'sms_credit' => $newCredit

                                ]);

                            return $thereturnedd;

                        } else {

                            return false;

                        }

                    }

                }

                return false; // Return false if user type is not permitted

            }

        }

        