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

    Route::get('usersapi', [ApiUsersController::class, 'getagents']);
    Route::get('customerssapi', [ApiUsersController::class, 'getcustomers']);
    Route::get('getcustomerbyid', [ApiUsersController::class, 'getcustomerbyid']);

    // --- NEW LOAN SYSTEM ROUTES (Moved from loans.php) ---
    Route::group(['prefix' => 'loans'], function () {
        // Treasury
        Route::get('capital-accounts', [CapitalAccountController::class, 'index']);
        Route::post('capital-accounts', [CapitalAccountController::class, 'store']);
        Route::post('capital-accounts/add-funds', [CapitalAccountController::class, 'addFunds']);
        Route::get('capital-accounts/{id}/history', [CapitalAccountController::class, 'getHistory']);
        Route::post('capital-accounts/transaction/{id}/update', [CapitalAccountController::class, 'updateTransaction']);
        Route::post('capital-accounts/adjust-balance', [CapitalAccountController::class, 'adjustBalance']);
        Route::get('pool-balance', [CapitalAccountController::class, 'getPoolBalance']);
        Route::post('fund-transfer', [CapitalAccountController::class, 'transferToPool']);
        Route::get('fund-transfer/history', [CapitalAccountController::class, 'getPoolTransferHistory']);

        // Products
        Route::get('fees', [LoanProductController::class, 'getFees']);
        Route::post('fees', [LoanProductController::class, 'storeFee']);
        Route::put('fees/{id}', [LoanProductController::class, 'updateFee']);
        Route::delete('fees/{id}', [LoanProductController::class, 'deleteFee']);
        Route::get('products', [LoanProductController::class, 'index']);
        Route::post('products', [LoanProductController::class, 'store']);

        // Applications
        Route::get('applications/active', [App\Http\Controllers\LoanApplicationController::class, 'getActiveLoans']); // New route for active loans
        Route::post('applications/{id}/transfer', [App\Http\Controllers\LoanApplicationController::class, 'transferLoan']); // New route for loan transfer
        Route::get('applications', [App\Http\Controllers\LoanApplicationController::class, 'index']);
        Route::put('applications/{id}', [App\Http\Controllers\LoanApplicationController::class, 'update']); // New route for updating application
        Route::get('applications/{id}', [App\Http\Controllers\LoanApplicationController::class, 'show']); // New route for single application
        Route::post('applications/{id}/approve', [App\Http\Controllers\LoanApprovalController::class, 'approve']);
        Route::post('applications/{id}/reject', [App\Http\Controllers\LoanApprovalController::class, 'reject']);
        Route::post('applications/{id}/disburse', [App\Http\Controllers\LoanDisbursementController::class, 'disburse']);
        Route::post('calculate-application', [App\Http\Controllers\LoanApplicationController::class, 'calculate']);
        Route::post('submit-application', [App\Http\Controllers\LoanApplicationController::class, 'store']);
        
        // Processing
        Route::get('applications/{id}/requirements', [App\Http\Controllers\LoanProcessingController::class, 'index']);
        Route::post('requirements/{id}/toggle', [App\Http\Controllers\LoanProcessingController::class, 'toggle']);
        Route::post('requirements/upload', [App\Http\Controllers\LoanProcessingController::class, 'upload']);
        Route::post('applications/{id}/submit-for-approval', [App\Http\Controllers\LoanProcessingController::class, 'submit']); // New route

        // Repayments
        Route::post('applications/{id}/repay', [App\Http\Controllers\LoanRepaymentController::class, 'store']); // This is the repayment action
        Route::get('applications/{loanId}/repayments/{transactionId}/receipt', [App\Http\Controllers\LoanRepaymentController::class, 'getRepaymentReceipt']); 
        
        // History & Statement
        Route::get('applications/{id}/history', [App\Http\Controllers\LoanRepaymentController::class, 'getHistory']);
        Route::get('applications/{id}/statement', [App\Http\Controllers\LoanRepaymentController::class, 'getStatement']);
    });
    // ----------------------------------------------------

    // --- AGENT COMMISSIONS ---
    Route::group(['prefix' => 'commissions'], function () {
        Route::get('search-agents', [ApiUsersController::class, 'searchAgents']);
        Route::get('search-users-for-payout', [ApiUsersController::class, 'searchUsersForPayout']);
        Route::get('summary', [App\Http\Controllers\CommissionController::class, 'summary']);
        Route::get('{agentId}/history', [App\Http\Controllers\CommissionController::class, 'history']);
        Route::post('payout', [App\Http\Controllers\CommissionController::class, 'payout']);
        Route::post('settings', [App\Http\Controllers\CommissionController::class, 'updateSettings']);
    });
    // -------------------------

    // --- NEW LOAN SYSTEM DEFAULT MANAGEMENT ---
    Route::group(['prefix' => 'loans'], function () {
        Route::post('{id}/log-default-action', [App\Http\Controllers\LoanDefaultController::class, 'logAction']);
    });
    // ------------------------------------------

    // --- NEW LOAN SYSTEM REPORTS ---
    Route::group(['prefix' => 'reports'], function () {
        Route::get('loan-dashboard-metrics', [App\Http\Controllers\LoanReportController::class, 'getLoanDashboardMetrics']);
        Route::get('defaulted-loans', [App\Http\Controllers\LoanReportController::class, 'getActualDefaultedLoans']); // New route for defaulted loans
    });
    // -------------------------------

    // --- SYSTEM MAINTENANCE ---
    Route::group(['prefix' => 'maintenance'], function () {
        Route::post('reset-loans', 'SystemMaintenanceController@resetLoanSystem');
    });
    // --------------------------

    //Customer  Search

    // Role and Permissions Setup
    Route::post('roles/assign', [App\Http\Controllers\RoleSetupController::class, 'assignRole']);
    Route::get('roles', [App\Http\Controllers\RoleSetupController::class, 'getRoles']);

    //Customer  Search
    Route::get('searchcustomerssapi', [ApiUsersController::class, 'searchgetcustomers']);
    Route::get('searchcustomerbyaccountnumber', [ApiUsersController::class, 'searchbyaccountnumber']);

    Route::get('accountlistapi', [ApiUsersController::class, 'getaccountlist']);
    Route::get('loanaccountlistapi', [ApiUsersController::class, 'getloanaccountlist']);
    Route::get('getdashboardlisttoday', [ApiUsersController::class, 'getdashboardlisttoday']);
    Route::get('getdashboardlistthisweek', [ApiUsersController::class, 'getdashboardlistthisweek']);
    Route::get('getdashboardlistthismonth', [ApiUsersController::class, 'getdashboardlistthismonth']);
    Route::get('getdashboardlistthisyear', [ApiUsersController::class, 'getdashboardlistthisyear']);
    Route::get('getdepositlist', [ApiUsersController::class, 'getdepositlist']);
    Route::get('getwithdrawallist', [ApiUsersController::class, 'getwithdrawallist']);
    Route::get('getreversallist', [ApiUsersController::class, 'getreversallist']);
    Route::get('getloanrequests', [ApiUsersController::class, 'getloanrequests']);
    Route::get('getdashboardlistalltime', [ApiUsersController::class, 'getdashboardlistalltime']);


    //Withdrawal Requests
    Route::get('withdrawalrequests', [ApiUsersController::class, 'withdrawalrequests']);
    Route::get('approve_withdrawal_request', [ApiUsersController::class, 'approve_withdrawal_request']);
    Route::get('withdrawtransaction', [ApiUsersController::class, 'withdrawtransaction']);
    Route::get('paywithdrawalcustomer', [ApiUsersController::class, 'paywithdrawalcustomer']);
    Route::get('withdrawtransaction_susu', [ApiUsersController::class, 'withdrawtransaction_susu']);
    //withdrawtransaction_susu

    //For customer
    Route::post('registeruser', [ApiUsersController::class, 'registeruserac']);
    Route::post('registeruseredit', [ApiUsersController::class, 'registeruserac_update']);


    //getting the user account information
    //registersystemuser
    Route::get('updatesystemuser', [ApiUsersController::class, 'updatesystemuser']);
    Route::get('registersystemuser', [ApiUsersController::class, 'registersystemuser']);
    Route::get('getuseraccountnumbers', [ApiUsersController::class, 'getuseraccountnumbers']);
    Route::get('getusertransactions', [ApiUsersController::class, 'getusertransactions']);
    Route::get('getaccounttypes', [ApiUsersController::class, 'getaccounttypes']);
    Route::get('getaccounttypes2', [ApiUsersController::class, 'getaccounttypes2']);
    Route::get('changeaccounttypes', [ApiUsersController::class, 'changeaccounttypes']);
    
    Route::get('getcustomerinfo', [ApiUsersController::class, 'getcustomerinfo']);

    Route::get('deposittransaction', [ApiUsersController::class, 'deposittransaction']);

    //get printing data
    Route::get('getprintedstatements', [ApiUsersController::class, 'getprintedstatements']);
    Route::get('getprintedtransactions', [ApiUsersController::class, 'getprintedtransactions']);
    Route::get('getprintedtransactionsbyid', [ApiUsersController::class, 'getprintedtransactionsbyid']);
    Route::get('getprinteddeposits', [ApiUsersController::class, 'getprinteddeposits']);
    Route::get('getprintedwithdrawals', [ApiUsersController::class, 'getprintedwithdrawals']);
    Route::get('getprintedaccountbalance', [ApiUsersController::class, 'getprintedaccountbalance']);


    //account number data
    Route::get('addaccounttouser', [ApiUsersController::class, 'addaccounttouser']);
    Route::get('updateaccounttouser', [ApiUsersController::class, 'updatecustomer_accounttype']);
    Route::get('getcustomeraccountslist', [ApiUsersController::class, 'getcustomeraccountslist']);

    // accounts
    Route::post('updatesavingsaccount', [ApiUsersController::class, 'updatesavingsaccount']);
    Route::post('insertsavingsaccount', [ApiUsersController::class, 'insertsavingsaccount']);
    Route::post('deletesavingsaccount', [ApiUsersController::class, 'deletesavingsaccount']);

    //for susu accounts
    Route::get('checkifsusuaccount', [ApiUsersController::class, 'checkifsusuaccount']);
    Route::get('getsusuaccount', [ApiUsersController::class, 'getsusuaccount']);
    Route::get('deposittransaction_susu', [ApiUsersController::class, 'deposittransaction_susu']);
    Route::get('completesusu', [ApiUsersController::class, 'completesusu']);
 
    //for company account
    Route::get('updatecompanyinfo', [ApiUsersController::class, 'updatecompanyinfo']);

    Route::get('sendconfirmationcode', [ApiUsersController::class, 'sendconfirmationcode']);

    //Daily Collections
Route::get('/getdailycollections', 'ApiUsersController@getdailycollections');
Route::get('/getdailycollectionswithdraw', 'ApiUsersController@getdailycollectionswithdraw');
Route::get('/getdailycollectionsloanrepayment', 'ApiUsersController@getdailycollectionsloanrepayment');
    Route::get('agentmobilizationbyproducts', [ApiUsersController::class, 'agentmobilizationbyproducts']);
    Route::get('agentmobilizationbyproductswithdrawals', [ApiUsersController::class, 'agentmobilizationbyproductswithdrawals']);

    //Getting the account info from the account number like commission value and minimum balance.
    Route::get('getaccountbalance', [ApiUsersController::class, 'getaccountbalance']);
    Route::get('getcommissionvalue', [ApiUsersController::class, 'getcommissionvalue']);
    Route::get('getaccountbalaceandcharges', [ApiUsersController::class, 'getaccountbalaceandcharges']);

    //For sms credit transactions

    Route::get('company_sms_transaction2', [ApiUsersController::class, 'company_sms_transaction2']);

    
    //for company packages
    
    Route::get('get_system_products_for_clients', [ApiUsersController::class, 'get_system_products_for_clients']);

    //monthlydeductions
    Route::get('monthlydeductions', [ApiUsersController::class, 'monthlydeductions']);

    //for reports
    Route::get('gen_systemreports', [ApiUsersController::class, 'gen_systemreports']);

    
});

// Existing Routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('insertcompanyinfo', [ApiUsersController::class, 'insertcompanyinfo']);
Route::get('mymtn', [ApiUsersController::class, 'mymtn']);

// Scheduler Endpoint for Cron Jobs (Secured by custom middleware)
Route::get('/schedule/run-loan-cron', [App\Http\Controllers\SchedulerController::class, 'triggerLoanCron'])->middleware('cron.secret');

