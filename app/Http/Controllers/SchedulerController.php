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

            // 7. Company Isolation for Loans (Bug Fix Sprint 9)
            $loanAppTable = 'loan_applications';
            if (Schema::hasTable($loanAppTable) && !Schema::hasColumn($loanAppTable, 'comp_id')) {
                Schema::table($loanAppTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_comp_id');
                });
                // Backfill existing loan_applications. Assuming a default comp_id of 2 for existing data.
                DB::table($loanAppTable)->update(['comp_id' => 2]);
            }
            // Sprint 10: Multi-tenancy - Add comp_id to all relevant tables

            // nobs_registration
            $nobsRegTable = 'nobs_registration';
            if (Schema::hasTable($nobsRegTable) && !Schema::hasColumn($nobsRegTable, 'comp_id')) {
                Schema::table($nobsRegTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_nobs_registration_comp_id');
                });
                DB::table($nobsRegTable)->update(['comp_id' => 2]);
            }

            // account_types
            $accountTypesTable = 'account_types';
            if (Schema::hasTable($accountTypesTable) && !Schema::hasColumn($accountTypesTable, 'comp_id')) {
                Schema::table($accountTypesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_account_types_comp_id');
                });
                DB::table($accountTypesTable)->update(['comp_id' => 2]);
            }

            // agent_commissions
            $agentCommissionsTable = 'agent_commissions';
            if (Schema::hasTable($agentCommissionsTable) && !Schema::hasColumn($agentCommissionsTable, 'comp_id')) {
                Schema::table($agentCommissionsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_agent_commissions_comp_id');
                });
                DB::table($agentCommissionsTable)->update(['comp_id' => 2]);
            }

            // capital_accounts
            $capitalAccountsTable = 'capital_accounts';
            if (Schema::hasTable($capitalAccountsTable) && !Schema::hasColumn($capitalAccountsTable, 'comp_id')) {
                Schema::table($capitalAccountsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_capital_accounts_comp_id');
                });
                DB::table($capitalAccountsTable)->update(['comp_id' => 2]);
            }

            // capital_account_transactions
            $capitalAccountTransactionsTable = 'capital_account_transactions';
            if (Schema::hasTable($capitalAccountTransactionsTable) && !Schema::hasColumn($capitalAccountTransactionsTable, 'comp_id')) {
                Schema::table($capitalAccountTransactionsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_capital_account_transactions_comp_id');
                });
                DB::table($capitalAccountTransactionsTable)->update(['comp_id' => 2]);
            }

            // central_loan_accounts
            $centralLoanAccountsTable = 'central_loan_accounts';
            if (Schema::hasTable($centralLoanAccountsTable) && !Schema::hasColumn($centralLoanAccountsTable, 'comp_id')) {
                Schema::table($centralLoanAccountsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_central_loan_accounts_comp_id');
                });
                DB::table($centralLoanAccountsTable)->update(['comp_id' => 2]);
            }

            // commission_payouts
            $commissionPayoutsTable = 'commission_payouts';
            if (Schema::hasTable($commissionPayoutsTable) && !Schema::hasColumn($commissionPayoutsTable, 'comp_id')) {
                Schema::table($commissionPayoutsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_commission_payouts_comp_id');
                });
                DB::table($commissionPayoutsTable)->update(['comp_id' => 2]);
            }

            // coupons
            $couponsTable = 'coupons';
            if (Schema::hasTable($couponsTable) && !Schema::hasColumn($couponsTable, 'comp_id')) {
                Schema::table($couponsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_coupons_comp_id');
                });
                DB::table($couponsTable)->update(['comp_id' => 2]);
            }

            // documents
            $documentsTable = 'documents';
            if (Schema::hasTable($documentsTable) && !Schema::hasColumn($documentsTable, 'comp_id')) {
                Schema::table($documentsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_documents_comp_id');
                });
                DB::table($documentsTable)->update(['comp_id' => 2]);
            }

            // document_folders
            $documentFoldersTable = 'document_folders';
            if (Schema::hasTable($documentFoldersTable) && !Schema::hasColumn($documentFoldersTable, 'comp_id')) {
                Schema::table($documentFoldersTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_document_folders_comp_id');
                });
                DB::table($documentFoldersTable)->update(['comp_id' => 2]);
            }

            // document_types
            $documentTypesTable = 'document_types';
            if (Schema::hasTable($documentTypesTable) && !Schema::hasColumn($documentTypesTable, 'comp_id')) {
                Schema::table($documentTypesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_document_types_comp_id');
                });
                DB::table($documentTypesTable)->update(['comp_id' => 2]);
            }

            // fund_transfers
            $fundTransfersTable = 'fund_transfers';
            if (Schema::hasTable($fundTransfersTable) && !Schema::hasColumn($fundTransfersTable, 'comp_id')) {
                Schema::table($fundTransfersTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_fund_transfers_comp_id');
                });
                DB::table($fundTransfersTable)->update(['comp_id' => 2]);
            }

            // invoices
            $invoicesTable = 'invoices';
            if (Schema::hasTable($invoicesTable) && !Schema::hasColumn($invoicesTable, 'comp_id')) {
                Schema::table($invoicesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_invoices_comp_id');
                });
                DB::table($invoicesTable)->update(['comp_id' => 2]);
            }

            // invoice_items
            $invoiceItemsTable = 'invoice_items';
            if (Schema::hasTable($invoiceItemsTable) && !Schema::hasColumn($invoiceItemsTable, 'comp_id')) {
                Schema::table($invoiceItemsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_invoice_items_comp_id');
                });
                DB::table($invoiceItemsTable)->update(['comp_id' => 2]);
            }

            // loan_application_requirements
            $loanApplicationRequirementsTable = 'loan_application_requirements';
            if (Schema::hasTable($loanApplicationRequirementsTable) && !Schema::hasColumn($loanApplicationRequirementsTable, 'comp_id')) {
                Schema::table($loanApplicationRequirementsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_application_requirements_comp_id');
                });
                DB::table($loanApplicationRequirementsTable)->update(['comp_id' => 2]);
            }

            // loan_default_logs
            $loanDefaultLogsTable = 'loan_default_logs';
            if (Schema::hasTable($loanDefaultLogsTable) && !Schema::hasColumn($loanDefaultLogsTable, 'comp_id')) {
                Schema::table($loanDefaultLogsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_default_logs_comp_id');
                });
                DB::table($loanDefaultLogsTable)->update(['comp_id' => 2]);
            }

            // loan_fees
            $loanFeesTable = 'loan_fees';
            if (Schema::hasTable($loanFeesTable) && !Schema::hasColumn($loanFeesTable, 'comp_id')) {
                Schema::table($loanFeesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_fees_comp_id');
                });
                DB::table($loanFeesTable)->update(['comp_id' => 2]);
            }

            // loan_products
            $loanProductsTable = 'loan_products';
            if (Schema::hasTable($loanProductsTable) && !Schema::hasColumn($loanProductsTable, 'comp_id')) {
                Schema::table($loanProductsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_products_comp_id');
                });
                DB::table($loanProductsTable)->update(['comp_id' => 2]);
            }

            // loan_repayment_schedules
            $loanRepaymentSchedulesTable = 'loan_repayment_schedules';
            if (Schema::hasTable($loanRepaymentSchedulesTable) && !Schema::hasColumn($loanRepaymentSchedulesTable, 'comp_id')) {
                Schema::table($loanRepaymentSchedulesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_loan_repayment_schedules_comp_id');
                });
                DB::table($loanRepaymentSchedulesTable)->update(['comp_id' => 2]);
            }

            // nobs_savings_accounts
            $nobsSavingsAccountsTable = 'nobs_savings_accounts';
            if (Schema::hasTable($nobsSavingsAccountsTable) && !Schema::hasColumn($nobsSavingsAccountsTable, 'comp_id')) {
                Schema::table($nobsSavingsAccountsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_nobs_savings_accounts_comp_id');
                });
                DB::table($nobsSavingsAccountsTable)->update(['comp_id' => 2]);
            }

            // nobs_susu_cycles
            $nobsSusuCyclesTable = 'nobs_susu_cycles';
            if (Schema::hasTable($nobsSusuCyclesTable) && !Schema::hasColumn($nobsSusuCyclesTable, 'comp_id')) {
                Schema::table($nobsSusuCyclesTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_nobs_susu_cycles_comp_id');
                });
                DB::table($nobsSusuCyclesTable)->update(['comp_id' => 2]);
            }

            // nobs_user_account_numbers
            $nobsUserAccountNumbersTable = 'nobs_user_account_numbers';
            if (Schema::hasTable($nobsUserAccountNumbersTable) && !Schema::hasColumn($nobsUserAccountNumbersTable, 'comp_id')) {
                Schema::table($nobsUserAccountNumbersTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_nobs_user_account_numbers_comp_id');
                });
                DB::table($nobsUserAccountNumbersTable)->update(['comp_id' => 2]);
            }

            // user_defualt_views
            $userDefualtViewsTable = 'user_defualt_views';
            if (Schema::hasTable($userDefualtViewsTable) && !Schema::hasColumn($userDefualtViewsTable, 'comp_id')) {
                Schema::table($userDefualtViewsTable, function (Blueprint $table) {
                    $table->integer('comp_id')->nullable()->after('id');
                    $table->index('comp_id', 'idx_user_defualt_views_comp_id');
                });
                DB::table($userDefualtViewsTable)->update(['comp_id' => 2]);
            }

            return response()->json(['success' => true, 'message' => 'System performance optimizations and fixes applied successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Setup Failed: ' . $e->getMessage() . ' on Line ' . $e->getLine()
            ], 500);
        }
    }

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