<?php

namespace App\Services;

use App\LoanApplication;
use App\LoanRepaymentSchedule;
use App\LoanDefaultLog;
use App\CompanyInfo;
use App\SmsLog;
use App\User;
use App\Http\Controllers\ApiUsersController; // Reuse existing SMS logic
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanCronService
{
    /**
     * The main entry point for the daily process.
     */
    public function runDailyProcess($companyId)
    {
        $log = [];
        $today = Carbon::today();

        DB::beginTransaction();
        try {
            // 1. Mark Overdue Schedules
            $overdueCount = $this->markOverdueSchedules($companyId);
            $log['overdue_marked'] = $overdueCount;

            // 2. Flip Loans to Default
            $defaultsCount = $this->updateLoanDefaults($companyId);
            $log['defaults_marked'] = $defaultsCount;

            // 3. Send Pre-Payment Reminders (3 Days Before)
            $remindersSent = $this->sendPrePaymentReminders($companyId);
            $log['reminders_sent'] = $remindersSent;

            // 4. Send Default Alerts (Just Defaulted)
            $alertsSent = $this->sendDefaultAlerts($companyId);
            $log['default_alerts_sent'] = $alertsSent;

            DB::commit();

            return [
                'success' => true,
                'log' => $log,
                'message' => 'Daily process completed successfully.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Loan Cron Failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function markOverdueSchedules($companyId)
    {
        return 0; // Skipping explicit DB status change for Schedule to avoid breaking legacy queries.
    }

    private function updateLoanDefaults($companyId)
    {
        $activeLoans = LoanApplication::where('status', 'active')
            ->whereHas('customer', function($q) use ($companyId) {
                $q->where('comp_id', $companyId);
            })
            ->get();

        $count = 0;

        foreach ($activeLoans as $loan) {
            $overdueSchedules = $loan->repaymentSchedules()
                ->where('status', 'pending') 
                ->where('due_date', '<', Carbon::now()->subDays(1)) 
                ->count();

            if ($overdueSchedules >= 1) {
                $loan->status = 'defaulted';
                $loan->save();

                LoanDefaultLog::create([
                    'comp_id' => $companyId,
                    'loan_application_id' => $loan->id,
                    'action_type' => 'auto_default',
                    'description' => "System marked as defaulted. Overdue schedules: $overdueSchedules",
                    'created_by' => 1 
                ]);

                $count++;
            }
        }

        return $count;
    }

    private function sendPrePaymentReminders($companyId)
    {
        $companyInfo = CompanyInfo::find($companyId);
        // Check if automatic SMS is enabled
        if (!$companyInfo || !$companyInfo->auto_sms_enabled) {
            return 0;
        }

        $targetDate = Carbon::today()->addDays(3);

        $schedules = LoanRepaymentSchedule::whereDate('due_date', $targetDate)
            ->where('status', 'pending')
            ->whereHas('application', function($q) {
                $q->where('status', 'active');
            })
            ->with(['application.customer'])
            ->get();

        $sent = 0;
        $smsController = new ApiUsersController(); 

        foreach ($schedules as $schedule) {
            $customer = $schedule->application->customer;
            if ($customer && $customer->comp_id == $companyId) {
                
                $msg = "Reminder: Your loan payment of {$schedule->total_due} is due on " . Carbon::parse($schedule->due_date)->format('d-M-Y') . ". Please ensure your account is funded.";
                
                if ($companyInfo->sms_active && $companyInfo->sms_credit > 0) {
                    $res = $smsController->sendFrogMessage(
                        $companyInfo->sms_username,
                        $companyInfo->sms_password,
                        $companyInfo->sms_sender_id,
                        $msg,
                        $customer->phone_number
                    );
                    
                    // Log the SMS
                    SmsLog::create([
                        'comp_id' => $companyId,
                        'customer_id' => $customer->id,
                        'phone_number' => $customer->phone_number,
                        'message' => $msg,
                        'status' => 'sent',
                        'type' => 'reminder',
                        'api_response' => is_string($res) ? $res : json_encode($res)
                    ]);

                    $companyInfo->decrement('sms_credit');
                    $sent++;
                }
            }
        }
        return $sent;
    }
private function sendDefaultAlerts($companyId)
{
    $companyInfo = CompanyInfo::find($companyId);
    // Check if automatic SMS is enabled
    if (!$companyInfo || !$companyInfo->auto_sms_enabled) {
        return 0;
    }

    // Find loans marked defaulted TODAY for THIS company
    $logs = LoanDefaultLog::where('comp_id', $companyId)
        ->whereDate('created_at', Carbon::today())
        ->where('action_type', 'auto_default')
        ->with(['loan_application.customer'])
        ->get();

        $sent = 0;
        $smsController = new ApiUsersController();
        
        foreach ($logs as $log) {
            $loan = $log->loan_application;
            if (!$loan) continue;

            $customer = $loan->customer;
            if ($customer && $customer->comp_id == $companyId) {
                 if ($companyInfo->sms_active && $companyInfo->sms_credit > 0) {
                     $msg = "Alert: You missed your loan payment. Your loan is now in DEFAULT. Please pay immediately to avoid penalties.";
                     
                     $res = $smsController->sendFrogMessage(
                        $companyInfo->sms_username,
                        $companyInfo->sms_password,
                        $companyInfo->sms_sender_id,
                        $msg,
                        $customer->phone_number
                    );

                    // Log the SMS
                    SmsLog::create([
                        'comp_id' => $companyId,
                        'customer_id' => $customer->id,
                        'phone_number' => $customer->phone_number,
                        'message' => $msg,
                        'status' => 'sent',
                        'type' => 'alert',
                        'api_response' => is_string($res) ? $res : json_encode($res)
                    ]);

                    $companyInfo->decrement('sms_credit');
                    $sent++;
                 }
            }
        }

        return $sent;
    }
}
