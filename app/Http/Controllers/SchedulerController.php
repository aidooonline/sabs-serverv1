<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoanCronService;
use Illuminate\Support\Facades\Log; // For logging

class SchedulerController extends Controller
{
    /**
     * Trigger the LoanCronService to run scheduled tasks.
     * This endpoint should be secured externally (e.g., IP whitelist, secret key).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerLoanCron(Request $request)
    {
        // For security, you should implement a check here.
        // Example: check for a secret key in the request header or query parameter,
        // or ensure the request comes from a whitelisted IP address.
        // For this task, we'll assume a basic check, but advise the user for stronger security.

        $secretKey = env('LOAN_CRON_SECRET_KEY');
        if (!$secretKey || $request->header('X-Cron-Secret') !== $secretKey) {
            // Log an attempt of unauthorized access
            Log::warning('Unauthorized access attempt to triggerLoanCron endpoint.', ['ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }

        try {
            $loanCronService = new LoanCronService();
            $loanCronService->runScheduledTasks();

            Log::info('LoanCronService triggered successfully via API endpoint.');
            return response()->json(['success' => true, 'message' => 'Loan scheduled tasks triggered successfully.']);
        } catch (\Throwable $e) {
            Log::error('Error triggering LoanCronService via API endpoint: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Error triggering scheduled tasks.', 'error' => $e->getMessage()], 500);
        }
    }
}
