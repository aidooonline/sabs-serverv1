<?php

use App\Http\Controllers\CapitalAccountController;
use App\Http\Controllers\LoanProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Loan System Routes
|--------------------------------------------------------------------------
*/

// DEBUG ROUTE (Remove after verification)
Route::get('test-loan-route', function() {
    return 'Loans Loaded Successfully';
});

Route::group(['prefix' => 'loans', 'middleware' => ['auth:api']], function () {

    // --- Treasury (Sprint 1) ---
    Route::get('capital-accounts', [CapitalAccountController::class, 'index']);
    Route::post('capital-accounts', [CapitalAccountController::class, 'store']);
    Route::get('pool-balance', [CapitalAccountController::class, 'getPoolBalance']);
    Route::post('fund-transfer', [CapitalAccountController::class, 'transferToPool']);

    // --- Products (Sprint 1) ---
    Route::get('fees', [LoanProductController::class, 'getFees']);
    Route::post('fees', [LoanProductController::class, 'storeFee']);
    Route::get('products', [LoanProductController::class, 'index']);
    Route::post('products', [LoanProductController::class, 'storeProduct']);

});
