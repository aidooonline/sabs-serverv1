<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoanApplication;
use App\LoanDefaultLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckLoanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue loans and loans due for reminders, and update their status.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Checking loan statuses...');

        DB::transaction(function () {
            $this->handleReminders();
            $this->handleOverdueLoans();
        });

        $this->info('Loan status check complete.');
    }

    private function handleReminders()
    {
        $reminder_date = Carbon::now()->addDays(3)->toDateString();
        $this->info('Checking for loans due on ' . $reminder_date);

        $due_loans = LoanApplication::where('status', 'active')
            ->whereHas('repayment_schedules', function ($query) use ($reminder_date) {
                $query->where('due_date', '=', $reminder_date)
                      ->where('status', '!=', 'paid');
            })->get();
            
        foreach ($due_loans as $loan) {
            // Avoid sending duplicate reminders
            $existing_log = LoanDefaultLog::where('loan_application_id', $loan->id)
                                ->where('action_type', 'SMS_REMINDER')
                                ->whereDate('created_at', Carbon::today())
                                ->first();

            if (!$existing_log) {
                LoanDefaultLog::create([
                    'loan_application_id' => $loan->id,
                    'action_type' => 'SMS_REMINDER',
                    'description' => 'Automated reminder for payment due on ' . $reminder_date,
                    'created_by' => 0, // System User
                ]);
                $this->line('Reminder logged for Loan ID: ' . $loan->id);
            }
        }
    }

    private function handleOverdueLoans()
    {
        $today = Carbon::now()->toDateString();
        $this->info('Checking for loans overdue as of ' . $today);

        $overdue_loans = LoanApplication::where('status', 'active')
            ->whereHas('repayment_schedules', function ($query) use ($today) {
                $query->where('due_date', '<', $today)
                      ->where('status', '!=', 'paid');
            })->get();

        foreach ($overdue_loans as $loan) {
            $loan->status = 'defaulted';
            $loan->save();

            $existing_log = LoanDefaultLog::where('loan_application_id', $loan->id)
                                ->where('action_type', 'DEFAULT_MARKED')
                                ->first();

            if (!$existing_log) {
                 LoanDefaultLog::create([
                    'loan_application_id' => $loan->id,
                    'action_type' => 'DEFAULT_MARKED',
                    'description' => 'Loan automatically marked as defaulted due to overdue payment.',
                    'created_by' => 0, // System User
                ]);
            }
            $this->line('Loan ID: ' . $loan->id . ' marked as defaulted.');
        }
    }
}