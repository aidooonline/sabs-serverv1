<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoanCronService;
use App\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class SchedulerController extends Controller
{
    protected $cronService;

    public function __construct(LoanCronService $cronService)
    {
        $this->cronService = $cronService;
    }

    /**
     * One-time setup to add necessary columns to the company_info (accounts) table.
     * Workaround for missing CLI access.
     */
    public function setup()
    {
        if (!$this->checkPermission()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $tableName = 'accounts'; // CompanyInfo uses this table

            if (!Schema::hasColumn($tableName, 'loan_cron_last_run')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dateTime('loan_cron_last_run')->nullable();
                    $table->text('loan_cron_settings')->nullable(); // For future config
                    $table->boolean('loan_cron_enabled')->default(0);
                });
                return response()->json(['success' => true, 'message' => 'Scheduler columns added successfully.']);
            }

            return response()->json(['success' => true, 'message' => 'Setup already completed.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get current scheduler status.
     */
    public function status()
    {
        if (!$this->checkPermission()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $company = CompanyInfo::find(auth()->user()->comp_id);
        
        $lastRun = $company->loan_cron_last_run;
        $isRunToday = $lastRun ? Carbon::parse($lastRun)->isToday() : false;

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => (bool)$company->loan_cron_enabled,
                'last_run' => $lastRun,
                'is_up_to_date' => $isRunToday,
                'server_time' => now()->toDateTimeString()
            ]
        ]);
    }

    /**
     * Trigger the daily process manually or via "Soft Cron".
     */
    public function trigger(Request $request)
    {
        if (!$this->checkPermission()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $companyId = auth()->user()->comp_id;
        $company = CompanyInfo::find($companyId);
        
        // If auto-triggered (not forced), check if already ran today
        if (!$request->input('force')) {
            if ($company->loan_cron_last_run && Carbon::parse($company->loan_cron_last_run)->isToday()) {
                return response()->json(['success' => true, 'message' => 'Already ran today.', 'skipped' => true]);
            }
        }

        // Execute Service
        $result = $this->cronService->runDailyProcess($companyId);

        if ($result['success']) {
            // Update last run date
            $company->loan_cron_last_run = now();
            $company->save();
        }

        return response()->json($result);
    }

    /**
     * Update Scheduler Settings (Enable/Disable).
     */
    public function updateSettings(Request $request)
    {
        if (!$this->checkPermission()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $company = CompanyInfo::find(auth()->user()->comp_id);
        $company->loan_cron_enabled = $request->input('enabled');
        $company->save();

        return response()->json(['success' => true, 'message' => 'Settings updated.']);
    }

    private function checkPermission()
    {
        $user = auth()->user();
        // Allow Admin, Owner, Super Admin, Manager.
        // Deny Agent.
        
        // Check if user is explicitly an Agent type
        if (in_array($user->type, ['Agent', 'Agents', 'agent'])) {
            return false;
        }

        // Allow if role matches
        // Note: Project uses both 'type' string and Spatie roles. 
        // Logic: If NOT agent, proceed.
        return true;
    }
}