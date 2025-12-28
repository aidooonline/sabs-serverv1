<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoanCronService; // Import the service
use Illuminate\Support\Facades\Log; // For logging

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
    protected $description = 'Checks loan statuses, sends reminders/warnings, and marks defaults.';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Starting loan status check...');
        Log::info('Loan status check started.');

        $loanCronService = new LoanCronService();

        try {
            // Send payment reminders for loans due in 7 days
            $this->info('Sending payment reminders...');
            $loanCronService->sendPaymentReminders(7);
            $this->info('Payment reminders sent.');
            Log::info('Payment reminders sent successfully.');

            // Mark overdue loans and send warnings (e.g., 30 days overdue to mark as defaulted)
            $this->info('Checking for overdue loans and sending warnings...');
            $loanCronService->markOverdueAndWarn(30);
            $this->info('Overdue checks complete.');
            Log::info('Overdue checks complete successfully.');

            $this->info('Loan status check finished successfully.');
            Log::info('Loan status check finished successfully.');
            return 0; // Command executed successfully
        } catch (\Throwable $e) {
            $this->error('An error occurred during loan status check: ' . $e->getMessage());
            Log::error('Loan status check failed: ' . $e->getMessage(), ['exception' => $e]);
            return 1; // Command failed
        }
    }
}
