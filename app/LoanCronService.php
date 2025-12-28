<?php

namespace App;

use App\Http\Controllers\ApiUsersController; // Import to access company_sms_transaction
use App\LoanApplication;
use App\LoanDefaultLog;
use App\User; // Assuming User model
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable; // For catching exceptions

class LoanCronService 
{
    private $apiUsersController;

    public function __construct()
    {
        // Instantiate ApiUsersController to use its methods
        // Note: In a real scenario, this should be refactored to a dedicated SmsService or Trait
        // to avoid instantiating a Controller in a Service, but adhering to existing code patterns.
        $this->apiUsersController = new ApiUsersController();
    }

    /**
     * Orchestrates the running of all scheduled loan-related tasks.
     *
     * @return void
     */
    public function runScheduledTasks(): void
    {
        $this->sendPaymentReminders(7); // Send reminders for loans due in 7 days
        $this->markOverdueAndWarn(30); // Check for overdue loans and mark as defaulted after 30 days
    }

    /**
     * Helper to send SMS messages, replicating sendFrogMessage logic.
     * Manages SMS credits.
     *
     * @param string $phoneNumber
     * @param string $message
     * @param int $companyId
     * @param int|null $userId  User who initiated the action, if applicable
     * @return bool
     */
    private function sendSms(string $phoneNumber, string $message, int $companyId, $userId = null): bool
    {
        try {
            // Get company info for SMS settings
            $companyInfo = CompanyInfo::find($companyId);

            if (!$companyInfo || !$companyInfo->sms_active || $companyInfo->sms_credit <= 0) {
                // SMS not active or no credit
                // Log this if necessary
                return false;
            }

            // Deduct SMS credit
            // The company_sms_transaction expects Auth::user().
            // This is a workaround to simulate Auth::user() for cron context.
            // In a real scenario, the SMS logic should be in a service that doesn't depend on Auth facade.
            $originalUser = auth()->user(); // Store original user if any
            $tempUser = User::find($userId); // Try to find a user if provided

            if (!$tempUser && $companyInfo->created_by) { // Fallback to company creator or first admin user
                 $tempUser = User::where('comp_id', $companyId)->where('type', 'Admin')->first();
                 if(!$tempUser) $tempUser = User::where('comp_id', $companyId)->first();
            }

            if ($tempUser) {
                auth()->login($tempUser); // Log in a temporary user for company_sms_transaction
            } else {
                // If no user context can be established, SMS credit deduction might fail.
                // For cron, it's problematic if no user is authenticated.
                // Revert to simply checking and decrementing credit if Auth::id() is problematic in cron.
                 if ($companyInfo->sms_credit > 0) {
                    $companyInfo->sms_credit -= 1;
                    $companyInfo->save();
                 } else {
                     return false; // No credit even after trying to deduct
                 }
            }


            $credittrans = $this->apiUsersController->company_sms_transaction('sub', 1);

            if ($tempUser) {
                if ($originalUser) {
                    auth()->login($originalUser); // Restore original user
                } else {
                    auth()->logout(); // Logout temp user if no original user
                }
            }


            if (!$credittrans) {
                // Failed to deduct credit
                return false;
            }

            $theusername = 'NYB'; // Hardcoded from ApiUsersController
            $thepass = 'Populaire123^'; // Hardcoded from ApiUsersController
            $thesenderid = $companyInfo->sms_sender_id ?? 'SABS'; // Fallback sender ID

            $baseUrl = 'https://banqpopulaire.website/nobsimages2/sendfrogmsg.php'; // Hardcoded from ApiUsersController

            $params = [
                'theusername' => $theusername,
                'thepass' => $thepass,
                'thesenderid' => $thesenderid,
                'themessage' => $message,
                'thenumbersent' => $phoneNumber,
            ];

            $queryString = http_build_query($params);
            $url = $baseUrl . '?' . $queryString;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                // Log cURL error
                return false;
            }
            curl_close($ch);

            // Assuming a successful response from the SMS gateway
            return true;
        } catch (Throwable $e) {
            // Log exception
            return false;
        }
    }

    /**
     * Send payment reminders for loans due in X days.
     *
     * @param int $daysInAdvance
     * @return void
     */
    public function sendPaymentReminders(int $daysInAdvance = 7): void
    {
        $dueDate = Carbon::now()->addDays($daysInAdvance)->format('Y-m-d');

        $loanApplications = LoanApplication::with(['customer', 'repayment_schedules'])
            ->where('status', 'active')
            ->whereHas('repayment_schedules', function ($query) use ($dueDate) {
                $query->whereDate('due_date', '=', $dueDate)
                      ->where('status', 'pending');
            })
            ->get();

        foreach ($loanApplications as $loan) {
            try {
                $customer = $loan->customer;
                if (!$customer || !$customer->phone_number) {
                    continue;
                }

                $message = "Dear {$customer->first_name}, your loan repayment of " . number_format($loan->total_repayment / $loan->duration, 2) . " is due on {$dueDate}. Please make your payment. SABS";
                
                if ($this->sendSms($customer->phone_number, $message, $customer->comp_id, $loan->created_by)) {
                     LoanDefaultLog::create([
                        'loan_application_id' => $loan->id,
                        'action_type' => 'SMS_REMINDER',
                        'description' => "Payment reminder sent for due date {$dueDate}.",
                        'created_by' => $loan->created_by, // Agent who created loan or system user
                    ]);
                }
            } catch (Throwable $e) {
                // Log error for this specific loan
            }
        }
    }

    /**
     * Mark overdue loans and send warnings.
     * Optional: Mark loan application status as 'defaulted' after certain period.
     *
     * @param int $overdueDaysThreshold  Number of days overdue before marking as defaulted
     * @return void
     */
    public function markOverdueAndWarn(int $overdueDaysThreshold = 30): void
    {
        $overdueLoans = LoanApplication::with(['customer', 'repayment_schedules'])
            ->where('status', 'active')
            ->whereHas('repayment_schedules', function ($query) {
                $query->where('due_date', '<', Carbon::now())
                      ->where('status', 'pending'); // Overdue and pending
            })
            ->get();

        foreach ($overdueLoans as $loan) {
            try {
                $customer = $loan->customer;
                if (!$customer || !$customer->phone_number) {
                    continue;
                }

                // Check for most overdue schedule item to determine exact overdue duration
                $oldestOverdueSchedule = $loan->repayment_schedules
                                            ->where('status', 'pending')
                                            ->where('due_date', '<', Carbon::now())
                                            ->sortBy('due_date')
                                            ->first();

                if (!$oldestOverdueSchedule) {
                    continue; // Should not happen if whereHas is correct
                }

                $daysOverdue = Carbon::parse($oldestOverdueSchedule->due_date)->diffInDays(Carbon::now());

                // Send immediate warning if just became overdue, or repeated warnings
                $message = "Dear {$customer->first_name}, your loan payment is {$daysOverdue} days overdue. Please make your payment immediately to avoid penalties. SABS";
                
                // Add logic to prevent spamming SMS if already warned today, etc.
                // For simplicity, we send every time cron runs if overdue.
                if ($this->sendSms($customer->phone_number, $message, $customer->comp_id, $loan->created_by)) {
                    LoanDefaultLog::create([
                        'loan_application_id' => $loan->id,
                        'action_type' => 'SMS_WARNING',
                        'description' => "Overdue warning sent. Loan is {$daysOverdue} days overdue.",
                        'created_by' => $loan->created_by,
                    ]);
                }

                // Mark loan as defaulted if exceeds threshold
                if ($daysOverdue >= $overdueDaysThreshold && $loan->status !== 'defaulted') {
                    $loan->status = 'defaulted';
                    $loan->save();

                    LoanDefaultLog::create([
                        'loan_application_id' => $loan->id,
                        'action_type' => 'DEFAULT_MARKED',
                        'description' => "Loan marked as defaulted after {$daysOverdue} days overdue.",
                        'created_by' => $loan->created_by,
                    ]);
                }

            } catch (Throwable $e) {
                // Log error for this specific loan
            }
        }
    }
}