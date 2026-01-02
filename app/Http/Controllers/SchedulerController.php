<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoanCronService;
use App\Services\SystemCronService;
use App\CompanyInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class SchedulerController extends Controller
{
    protected $loanCronService;
    protected $systemCronService;

    public function __construct(LoanCronService $loanCronService, SystemCronService $systemCronService)
    {
        $this->loanCronService = $loanCronService;
        $this->systemCronService = $systemCronService;
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

            // 1. Add Scheduler columns to accounts table
            if (!Schema::hasColumn($tableName, 'loan_cron_last_run')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dateTime('loan_cron_last_run')->nullable();
                    $table->text('loan_cron_settings')->nullable();
                    $table->boolean('loan_cron_enabled')->default(0);
                });
            }

            // 2. Add Indexes for Performance (Sprint 5 Optimization)
            Schema::table('loan_applications', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('loan_applications');
                
                if (!array_key_exists('idx_loan_app_status', $indexes)) {
                    $table->index('status', 'idx_loan_app_status');
                }
                if (!array_key_exists('idx_loan_app_start_date', $indexes)) {
                    $table->index('repayment_start_date', 'idx_loan_app_start_date');
                }
            });

            Schema::table('loan_repayment_schedules', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('loan_repayment_schedules');

                if (!array_key_exists('idx_loan_sched_status', $indexes)) {
                    $table->index('status', 'idx_loan_sched_status');
                }
                if (!array_key_exists('idx_loan_sched_due_date', $indexes)) {
                    $table->index('due_date', 'idx_loan_sched_due_date');
                }
            });

            // 3. Optimize Legacy Ledger (Sprint 6 Performance)
            Schema::table('nobs_transactions', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('nobs_transactions');

                if (!array_key_exists('idx_trans_comp_date', $indexes)) {
                    $table->index(['comp_id', 'created_at'], 'idx_trans_comp_date');
                }
                if (!array_key_exists('idx_trans_type_comp', $indexes)) {
                    $table->index(['name_of_transaction', 'comp_id'], 'idx_trans_type_comp');
                }
                if (!array_key_exists('idx_trans_user_comp', $indexes)) {
                    $table->index(['users', 'comp_id'], 'idx_trans_user_comp');
                }
                if (!array_key_exists('idx_trans_acc_no', $indexes)) {
                    $table->index('account_number', 'idx_trans_acc_no');
                }
            });

            // 4. Optimize Customer Registry
            Schema::table('nobs_registration', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('nobs_registration');

                if (!array_key_exists('idx_reg_acc_no', $indexes)) {
                    $table->index('account_number', 'idx_reg_acc_no');
                }
                if (!array_key_exists('idx_reg_phone', $indexes)) {
                    $table->index('phone_number', 'idx_reg_phone');
                }
            });

            // 5. Dual Storage Support (Sprint 8.3)
            $reqTable = 'loan_application_requirements';
            if (Schema::hasTable($reqTable) && !Schema::hasColumn($reqTable, 'file_path_original')) {
                Schema::table($reqTable, function (Blueprint $table) {
                    $table->text('file_path_original')->nullable()->after('file_path');
                });
            }

            // 6. Dormancy Tracking (Sprint 8.5)
            $userAcctTable = 'nobs_user_account_numbers';
            if (Schema::hasTable($userAcctTable) && !Schema::hasColumn($userAcctTable, 'account_status')) {
                Schema::table($userAcctTable, function (Blueprint $table) {
                    $table->string('account_status', 20)->default('active')->after('balance');
                    $table->timestamp('last_transaction_date')->nullable()->after('account_status');
                    $table->index('account_status', 'idx_acct_status');
                });
            }

            // 7. Company Isolation for Loans (Bug Fix Sprint 9)
            if (Schema::hasTable('loan_applications') && !Schema::hasColumn('loan_applications', 'comp_id')) {
                Schema::table('loan_applications', function (Blueprint $table) {
                    $table->integer('comp_id')->default(2)->after('id'); // Default 2 for legacy compatibility if needed
                    $table->index('comp_id', 'idx_loan_comp_id');
                });
            }

            return response()->json(['success' => true, 'message' => 'System performance optimizations applied successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Setup Failed: ' . $e->getMessage() . ' on Line ' . $e->getLine()
            ], 500);
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

        $company = CompanyInfo::find(auth('api')->user()->comp_id);
        
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

        $companyId = auth('api')->user()->comp_id;
        $company = CompanyInfo::find($companyId);
        
        // If auto-triggered (not forced), check if already ran today
        if (!$request->input('force')) {
            if ($company->loan_cron_last_run && Carbon::parse($company->loan_cron_last_run)->isToday()) {
                return response()->json(['success' => true, 'message' => 'Already ran today.', 'skipped' => true]);
            }
        }

        // Execute Services
        $loanResult = $this->loanCronService->runDailyProcess($companyId);
        $dormancyCount = $this->systemCronService->updateDormancyStatus();

        if ($loanResult['success']) {
            // Update last run date
            $company->loan_cron_last_run = now();
            $company->save();

            // Add dormancy count to log
            $loanResult['log']['dormant_accounts_flagged'] = $dormancyCount;
        }

        return response()->json($loanResult);
    }
    
    /**
     * Update Scheduler Settings (Enable/Disable).
     */
    public function updateSettings(Request $request)
    {
        if (!$this->checkPermission()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $company = CompanyInfo::find(auth('api')->user()->comp_id);
        $company->loan_cron_enabled = $request->input('enabled');
        $company->save();

        return response()->json(['success' => true, 'message' => 'Settings updated.']);
    }

    private function checkPermission()
    {
        $user = auth('api')->user();
        if (!$user) {
            return false;
        }

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