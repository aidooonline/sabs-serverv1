<?php

use App\Http\Controllers\ApiUsersController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CapitalAccountController;
use App\Http\Controllers\LoanProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('users/{id}/toggle-status', [ApiUsersController::class, 'toggleUserStatus']);
    Route::post('users/check-phone', [ApiUsersController::class, 'checkPhoneNumber']);
    Route::get('usersapi', [ApiUsersController::class, 'getagents']);
    Route::get('active-agents-list', [ApiUsersController::class, 'getActiveAgentsList']);
    Route::get('get-loan-customers', [ApiUsersController::class, 'getLoanCustomers']);
    Route::get('customerssapi', [ApiUsersController::class, 'getcustomers']);
    Route::get('getcustomerbyid', [ApiUsersController::class, 'getcustomerbyid']);

    // --- NEW LOAN SYSTEM ROUTES ---
    Route::group(['prefix' => 'loans'], function () {
        Route::get('capital-accounts', [CapitalAccountController::class, 'index']);
        Route::post('capital-accounts', [CapitalAccountController::class, 'store']);
        Route::post('capital-accounts/add-funds', [CapitalAccountController::class, 'addFunds']);
        Route::get('capital-accounts/{id}/history', [CapitalAccountController::class, 'getHistory']);
        Route::post('capital-accounts/transaction/{id}/update', [CapitalAccountController::class, 'updateTransaction']);
        Route::post('capital-accounts/adjust-balance', [CapitalAccountController::class, 'adjustBalance']);
        Route::get('pool-balance', [CapitalAccountController::class, 'getPoolBalance']);
        Route::post('fund-transfer', [CapitalAccountController::class, 'transferToPool']);
        Route::get('fund-transfer/history', [CapitalAccountController::class, 'getPoolTransferHistory']);

        Route::get('fees', [LoanProductController::class, 'getFees']);
        Route::post('fees', [LoanProductController::class, 'storeFee']);
        Route::put('fees/{id}', [LoanProductController::class, 'updateFee']);
        Route::delete('fees/{id}', [LoanProductController::class, 'deleteFee']);
        Route::get('products', [LoanProductController::class, 'index']);
        Route::post('products', [LoanProductController::class, 'store']);
        Route::delete('products/{id}', [LoanProductController::class, 'destroy']);

        Route::get('applications/active', [App\Http\Controllers\LoanApplicationController::class, 'getActiveLoans']);
        Route::get('applications/customer-history', [App\Http\Controllers\LoanApplicationController::class, 'getCustomerLoanHistory']);
        Route::get('applications/due-today', [App\Http\Controllers\LoanApplicationController::class, 'getLoansDueToday']);
        Route::post('applications/{id}/transfer', [App\Http\Controllers\LoanApplicationController::class, 'transferLoan']);
        Route::get('applications', [App\Http\Controllers\LoanApplicationController::class, 'index']);
        Route::put('applications/{id}', [App\Http\Controllers\LoanApplicationController::class, 'update']);
        Route::get('applications/{id}', [App\Http\Controllers\LoanApplicationController::class, 'show']);
        Route::post('applications/{id}/approve', [App\Http\Controllers\LoanApprovalController::class, 'approve']);
        Route::post('applications/{id}/reject', [App\Http\Controllers\LoanApprovalController::class, 'reject']);
        Route::post('applications/{id}/disburse', [App\Http\Controllers\LoanDisbursementController::class, 'disburse']);
        Route::post('calculate-application', [App\Http\Controllers\LoanApplicationController::class, 'calculate']);
        Route::post('submit-application', [App\Http\Controllers\LoanApplicationController::class, 'store']);
        
        Route::get('applications/{id}/requirements', [App\Http\Controllers\LoanProcessingController::class, 'index']);
        Route::post('requirements/{id}/toggle', [App\Http\Controllers\LoanProcessingController::class, 'toggle']);
        Route::post('requirements/upload', [App\Http\Controllers\LoanProcessingController::class, 'upload']);
        Route::post('applications/{id}/submit-for-approval', [App\Http\Controllers\LoanProcessingController::class, 'submit']);

        Route::post('applications/{id}/repay', [App\Http\Controllers\LoanRepaymentController::class, 'store']);
        Route::get('applications/{loanId}/repayments/{transactionId}/receipt', [App\Http\Controllers\LoanRepaymentController::class, 'getRepaymentReceipt']); 
        
        Route::get('applications/{id}/history', [App\Http\Controllers\LoanRepaymentController::class, 'getHistory']);
        Route::get('applications/{id}/statement', [App\Http\Controllers\LoanRepaymentController::class, 'getStatement']);
        Route::post('{id}/log-default-action', [App\Http\Controllers\LoanDefaultController::class, 'logAction']);
    });

    Route::group(['prefix' => 'commissions'], function () {
        Route::get('search-agents', [ApiUsersController::class, 'searchAgents']);
        Route::get('search-users-for-payout', [ApiUsersController::class, 'searchUsersForPayout']);
        Route::get('summary', [App\Http\Controllers\CommissionController::class, 'summary']);
        Route::get('{agentId}/history', [App\Http\Controllers\CommissionController::class, 'history']);
        Route::post('payout', [App\Http\Controllers\CommissionController::class, 'payout']);
        Route::post('settings', [App\Http\Controllers\CommissionController::class, 'updateSettings']);
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('daily-expected', [App\Http\Controllers\LoanReportController::class, 'getDailyExpected']);
        Route::get('daily-repayment-list', [App\Http\Controllers\LoanReportController::class, 'getDailyRepaymentList']);
        Route::get('loan-dashboard-metrics', [App\Http\Controllers\LoanReportController::class, 'getLoanDashboardMetrics']);
        Route::get('loan-dashboard/history', [App\Http\Controllers\LoanReportController::class, 'getDashboardTransactionHistory']);
        Route::get('defaulted-loans', [App\Http\Controllers\LoanReportController::class, 'getActualDefaultedLoans']);
        Route::get('operational-metrics', [App\Http\Controllers\SystemReportController::class, 'getOperationalMetrics']);
        Route::get('executive-summary', [App\Http\Controllers\SystemReportController::class, 'getExecutiveSummary']);
    });

    Route::group(['prefix' => 'report-system'], function () {
        Route::get('live', [App\Http\Controllers\ReportSystemController::class, 'getLiveReport']);
        Route::post('snapshot', [App\Http\Controllers\ReportSystemController::class, 'saveSnapshot']);
    });
});

// Outside auth for token-based browser access
Route::get('report-system/export', [App\Http\Controllers\ReportSystemController::class, 'exportCsv']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('insertcompanyinfo', [ApiUsersController::class, 'insertcompanyinfo']);
Route::get('mymtn', [ApiUsersController::class, 'mymtn']);

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('users', [App\Http\Controllers\Admin\UserManagementController::class, 'index']);
    Route::post('users', [App\Http\Controllers\Admin\UserManagementController::class, 'store']);
    Route::get('users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'show']);
    Route::put('users/{id}', [App\Http\Controllers\Admin\UserManagementController::class, 'update']);
    Route::post('users/{id}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus']);

    Route::get('roles', [App\Http\Controllers\Admin\RoleManagementController::class, 'index']);
    Route::get('permissions', [App\Http\Controllers\Admin\RoleManagementController::class, 'getAllPermissions']);
    Route::put('roles/{id}', [App\Http\Controllers\Admin\RoleManagementController::class, 'updatePermissions']);
});

Route::group(['prefix' => 'scheduler'], function () {
    Route::post('setup', [App\Http\Controllers\SchedulerController::class, 'setup']);
    Route::get('status', [App\Http\Controllers\SchedulerController::class, 'status']);
    Route::post('trigger', [App\Http\Controllers\SchedulerController::class, 'trigger']);
    Route::post('settings', [App\Http\Controllers\SchedulerController::class, 'updateSettings']);
});

// Reversal & Generic Routes
Route::get('searchcustomerssapi', [ApiUsersController::class, 'searchgetcustomers']);
Route::get('searchcustomerbyaccountnumber', [ApiUsersController::class, 'searchbyaccountnumber']);
Route::get('getdepositlist', [ApiUsersController::class, 'getdepositlist']);
Route::get('getwithdrawallist', [ApiUsersController::class, 'getwithdrawallist']);
Route::get('getreversallist', [ApiUsersController::class, 'getreversallist']);
Route::post('perform-reversal', [ApiUsersController::class, 'performReversal']);
Route::post('perform-loan-reversal', [ApiUsersController::class, 'performLoanReversal']);
Route::get('getaccounttypes', [ApiUsersController::class, 'getaccounttypes']);
Route::get('getcustomeraccountslist', [ApiUsersController::class, 'getcustomeraccountslist']);
Route::get('deposittransaction', [ApiUsersController::class, 'deposittransaction']);
Route::get('withdrawtransaction', [ApiUsersController::class, 'withdrawtransaction']);
