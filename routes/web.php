 <?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/register/{lang?}', 'Auth\RegisterController@showRegistrationForm')->name('register');
//Route::post('register', 'Auth\RegisterController@register')->name('register');
Route::get('/login/{lang?}', 'Auth\LoginController@showLoginForm')->name('login');

Route::get('/password/resets/{lang?}', 'Auth\LoginController@showLinkRequestForm')->name('change.langPass');
 

Route::get(
    '/', [
           'as' => 'dashboard',
           'uses' => 'LoansAccountsController@dashboard',
       ]
)->middleware(
    [
        'XSS',

    ]
);
Route::get(
    '/dashboard', [
               'as' => 'dashboard',
               'uses' => 'LoansAccountsController@dashboard',
           ]
)->middleware(
    [
        'XSS',
        'auth',
    ]
);


Route::group(['middleware' => ['auth', 'XSS']], function (){
    Route::get('change-language/{lang}', 'LanguageController@changeLanquage')->name('change.language')->middleware(['auth', 'XSS']);
    Route::get('manage-language/{lang}', 'LanguageController@manageLanguage')->name('manage.language')->middleware(['auth', 'XSS']);
    Route::post('store-language-data/{lang}', 'LanguageController@storeLanguageData')->name('store.language.data')->middleware(['auth', 'XSS']);
    Route::get('create-language', 'LanguageController@createLanguage')->name('create.language')->middleware(['auth', 'XSS']);
    Route::post('store-language', 'LanguageController@storeLanguage')->name('store.language')->middleware(['auth', 'XSS']);
    Route::delete('/lang/{lang}', 'LanguageController@destroyLang')->name('lang.destroy')->middleware(['auth', 'XSS']);
});

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('user/grid', 'UserController@grid')->name('user.grid');
    Route::resource('user', 'UserController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('permission', 'PermissionController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('role', 'RoleController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('account/grid', 'AccountController@grid')->name('account.grid');
    Route::resource('account', 'AccountController');
    Route::get('account/create/{type}/{id}', 'AccountController@create')->name('account.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('account_type', 'AccountTypeController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('account_industry', 'AccountIndustryController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('contact/grid', 'ContactController@grid')->name('contact.grid');
    Route::resource('contact', 'ContactController');
    Route::get('contact/create/{type}/{id}', 'ContactController@create')->name('contact.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('lead/grid', 'LeadController@grid')->name('lead.grid');
    
    Route::resource('lead', 'LeadController');
    Route::resource('transactions', 'TransactionController');
    Route::get('/transactionexports', 'TransactionController@exportCsv')->name('export.transactions');
    
    Route::post('lead/change-order', 'LeadController@changeorder')->name('lead.change.order');
    Route::get('lead/create/{type}/{id}', 'LeadController@create')->name('lead.create');
    Route::get('lead/{id}/show_convert', 'LeadController@showConvertToAccount')->name('lead.convert.account');
    Route::post('lead/{id}/convert', 'LeadController@convertToAccount')->name('lead.convert.to.account');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('lead_source', 'LeadSourceController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('opportunities_stage', 'OpportunitiesStageController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('commoncase/grid', 'CommonCaseController@grid')->name('commoncases.grid');
    Route::resource('commoncases', 'CommonCaseController');
    Route::get('commoncases/create/{type}/{id}', 'CommonCaseController@create')->name('commoncases.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('opportunities/grid', 'OpportunitiesController@grid')->name('opportunities.grid');
    Route::resource('opportunities', 'OpportunitiesController');
    Route::post('opportunities/change-order', 'OpportunitiesController@changeorder')->name('opportunities.change.order');
    Route::get('opportunities/create/{type}/{id}', 'OpportunitiesController@create')->name('opportunities.create');
}
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('case_type', 'CaseTypeController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('meeting/grid', 'MeetingController@grid')->name('meeting.grid');
    Route::post('meeting/getparent', 'MeetingController@getparent')->name('meeting.getparent');
    Route::resource('meeting', 'MeetingController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('call/grid', 'CallController@grid')->name('call.grid');
    Route::post('call/getparent', 'CallController@getparent')->name('call.getparent');
    Route::resource('call', 'CallController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('task/grid', 'TaskController@grid')->name('task.grid');
    Route::post('task/getparent', 'TaskController@getparent')->name('task.getparent');
    Route::resource('task', 'TaskController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('task_stage', 'TaskStageController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('document_folder', 'DocumentFolderController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('document_type', 'DocumentTypeController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('campaign_type', 'CampaignTypeController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('target_list', 'TargetListController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('document/grid', 'DocumentController@grid')->name('document.grid');
    Route::resource('document', 'DocumentController');
    Route::get('document/create/{type}/{id}', 'DocumentController@create')->name('document.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('campaign/grid', 'CampaignController@grid')->name('campaign.grid');
    Route::resource('campaign', 'CampaignController');
    Route::get('campaign/create/{type}/{id}', 'CampaignController@create')->name('campaign.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('quote/grid', 'QuoteController@grid')->name('quote.grid');
    Route::get('quote/{id}/convert', 'QuoteController@convert')->name('quote.convert');

    Route::get('quote/preview/{template}/{color}', 'QuoteController@previewQuote')->name('quote.preview');
    Route::post('quote/template/setting', 'QuoteController@saveQuoteTemplateSettings')->name('quote.template.setting');
    Route::get('quote/pdf/{id}', 'QuoteController@pdf')->name('quote.pdf')->middleware(['XSS']);

    Route::post('quote/getaccount', 'QuoteController@getaccount')->name('quote.getaccount');
    Route::get('quote/quoteitem/{id}', 'QuoteController@quoteitem')->name('quote.quoteitem');
    Route::post('quote/storeitem/{id}', 'QuoteController@storeitem')->name('quote.storeitem');
    Route::get('quote/quoteitem/edit/{id}', 'QuoteController@quoteitemEdit')->name('quote.quoteitem.edit');
    Route::post('quote/storeitem/edit/{id}', 'QuoteController@quoteitemUpdate')->name('quote.quoteitem.update');
    Route::get('quote/items', 'QuoteController@items')->name('quote.items');
    Route::delete('quote/items/{id}/delete', 'QuoteController@itemsDestroy')->name('quote.items.delete');
    Route::resource('quote', 'QuoteController');
    Route::get('quote/create/{type}/{id}', 'QuoteController@create')->name('quote.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('product/grid', 'ProductController@grid')->name('product.grid');
    Route::resource('product', 'ProductController');
}
);
//*** NOBS ROUTES */

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    
    
    Route::resource('accounts', 'RegisteruserController');
    Route::get('accounts/transactiondetails/{accountsid?}/', 'RegisteruserController@transactiondetails')->name('accounts.transactiondetails');
    
    Route::get('accounts/loanpayment/{accountsid?}/{depositer?}/{phn?}', 'RegisteruserController@loantransaction')->name('accounts.loan');
    Route::get('accounts/searchloanpayment/{depositer?}', 'RegisteruserController@searchloan')->name('accounts.searchloan');

   
    Route::get('accounts/deposit/{accountsid?}/{depositer?}/{phn?}', 'RegisteruserController@deposittransaction')->name('accounts.deposit');
    Route::get('accounts/searchdeposit/{depositer?}', 'RegisteruserController@searchdeposit')->name('accounts.searchdeposit');


    Route::get('accounts/refund/{accountsid?}/{depositer?}/{phn?}', 'RegisteruserController@refundtransaction')->name('accounts.refund');
    Route::get('accounts/searchrefund/{accountsid?}/{depositer?}', 'RegisteruserController@searchrefund')->name('accounts.searchrefund');
    
    Route::get('accounts/reversetransaction/{id?}', 'RegisteruserController@reversetransaction')->name('reverse.transaction');


    Route::get('accounts/withdraw/{accountsid?}/{withdrawer?}/{phn?}', 'RegisteruserController@withdrawtransaction')->name('accounts.withdraw');
    Route::get('accounts/searchwithdraw/{withdrawer?}', 'RegisteruserController@searchwithdrawer')->name('accounts.searchwithdrawer');

    Route::get('accounts/searchuser/{search?}/', 'RegisteruserController@searchcustomers')->name('accounts.searcher');
    Route::get('agents/list/', 'RegisteruserController@getagents')->name('agents.index'); 
    Route::get('agentdashboard/page/{agentid?}/{agentname?}', 'LoansAccountsController@agentdashboard')->name('agents.transactiondetails');
    Route::get('agentdashboardsingle/page/{agentid?}/{agentname?}/{nameoftransaction?}', 'LoansAccountsController@agentdashboardsingle')->name('agents.singletransactiondetails');
    
    Route::get('reportsview/page/{agentid?}/{agentname?}/{nameoftransaction?}', 'LoansAccountsController@agentquerylist')->name('reportsview');
    Route::get('reportsview/adminpage/{agentid?}/{agentname?}/{nameoftransaction?}', 'LoansAccountsController@adminquerylist')->name('admreportsview');
    
    Route::get('dashboardadmreportsview/adminpage/{agentid?}/{agentname?}/{nameoftransaction?}', 'LoansAccountsController@dashboardadminquerylist')->name('dashboardadmreportsview');
     
    Route::get('accounts/loanrequests/{searcher?}', 'RegisteruserController@loanrequests')->name('loanrequests.requests');
    Route::get('withdrawalrequests/list/', 'RegisteruserController@getwithdrawalrequests')->name('withdrawrequests.lists'); 
   

});



Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    
    
    Route::resource('savings', 'SavingsAccountsController');
    Route::get('savings/transactiondetails/{accountsid?}/', 'SavingsAccountsController@transactiondetails')->name('savings.transactiondetails');
    Route::get('savings/searchuser/{search?}/', 'SavingsAccountsController@searchcustomers')->name('savings.searcher');

});


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    
    Route::resource('loans', 'LoansAccountsController');
    Route::resource('loanrequests', 'LoanRequestsController');
    Route::resource('loanrequestdetail', 'LoanRequestDetailController');
    Route::resource('loanpurpose', 'LoanPurposeController');
    Route::resource('loanmigrations', 'LoanMigrationController');
    Route::get('loanmigrations/disbursedloan/{id?}', 'LoanMigrationController@disburseloan')->name("loanmigration.disburseloan");
    
    Route::resource('ledgergeneral', 'LedgerGeneralController');

    Route::get('ledgergeneralsub/{parentname?}/{parentid?}/{ledgername?}/{ledgertype?}', 'LedgerGeneralController@subledger')->name("subledger.list");
    Route::get('ledgergeneralsub/create/{parentname?}/{parentid?}/{ledgername?}/{ledgertype?}', 'LedgerGeneralController@createsubledger')->name("subledger.create");
    Route::post('ledgergeneralsub/storesubledger/', 'LedgerGeneralController@storesubledger')->name("ledger.storesub");

    
    Route::resource('ledgeraccounttypes', 'LedgerAccountTypesController');
    
    Route::get('loanmigrations/migrate/{loanrequestid?}/{customerid?}/{loanname?}', 'LoanMigrationController@create')->name("loanmigrations.migrate");
    Route::get('loanrequestdetail/detail/{id?}', 'LoanRequestDetailController@detail')->name("loanrequestdetail.detail");
    Route::get('editloan/loan', 'LoansAccountsController@loanedit')->name("loan.loanedit");
    
    Route::get('loans/transactiondetails/{accountsid?}/', 'LoansAccountsController@transactiondetails')->name('loans.transactiondetails');
    Route::get('loans/searchuser/{search?}/', 'LoansAccountsController@searchcustomers')->name('loans.searcher');
    Route::get('mydashboard/page/', 'LoansAccountsController@dashboard')->name('dashboard.index');
    Route::get('agentquerydashboard/page/{agentqueryid?}', 'LoansAccountsController@agentquerydashboard')->name('agentquerydashboard.index');
    Route::get('myreports/registeredcustomers/', 'LoansAccountsController@registeredcustomers')->name('reports.registeredcustomers');

});




//** END NOBS ROUTES */

Route::group(['middleware' => ['auth', 'XSS']], function (){
    Route::resource('plan', 'PlanController');
    
});
Route::get('user/{id}/plan', 'UserController@upgradePlan')->name('plan.upgrade')->middleware(['auth', 'XSS']);
Route::get('user/{id}/plan/{pid}', 'UserController@activePlan')->name('plan.active')->middleware(['auth', 'XSS']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('product_category', 'ProductCategoryController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('product_brand', 'ProductBrandController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('product_tax', 'ProductTaxController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::resource('shipping_provider', 'ShippingProviderController');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::post('streamstore/{type}/{id}/{title}', 'StreamController@streamstore')->name('streamstore');
    Route::resource('stream', 'StreamController');


}
);
Route::get('calendar/{type?}', 'CalenderController@index')->name('calendar.index')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('salesorder/grid', 'SalesOrderController@grid')->name('salesorder.grid');

    Route::get('salesorder/preview/{template}/{color}', 'SalesOrderController@previewInvoice')->name('salesorder.preview');
    Route::post('salesorder/template/setting', 'SalesOrderController@saveSalesorderTemplateSettings')->name('salesorder.template.setting');
    Route::get('salesorder/pdf/{id}', 'SalesOrderController@pdf')->name('salesorder.pdf')->middleware(['XSS']);

    Route::post('salesorder/getaccount', 'SalesOrderController@getaccount')->name('salesorder.getaccount');
    Route::get('salesorder/salesorderitem/{id}', 'SalesOrderController@salesorderitem')->name('salesorder.salesorderitem');
    Route::post('salesorder/storeitem/{id}', 'SalesOrderController@storeitem')->name('salesorder.storeitem');
    Route::get('salesorder/items', 'SalesOrderController@items')->name('salesorder.items');

    Route::get('salesorder/item/edit/{id}', 'SalesOrderController@salesorderItemEdit')->name('salesorder.item.edit');
    Route::post('salesorder/item/edit/{id}', 'SalesOrderController@salesorderItemUpdate')->name('salesorder.item.update');

    Route::delete('salesorder/items/{id}/delete', 'SalesOrderController@itemsDestroy')->name('salesorder.items.delete');
    Route::resource('salesorder', 'SalesOrderController');
    Route::get('salesorder/create/{type}/{id}', 'SalesOrderController@create')->name('salesorder.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('invoice/grid', 'InvoiceController@grid')->name('invoice.grid');

    Route::get('invoice/preview/{template}/{color}', 'InvoiceController@previewInvoice')->name('invoice.preview');
    Route::post('invoice/template/setting', 'InvoiceController@saveInvoiceTemplateSettings')->name('invoice.template.setting');
    Route::get('invoice/pdf/{id}', 'InvoiceController@pdf')->name('invoice.pdf')->middleware(['XSS']);


    Route::post('invoice/getaccount', 'InvoiceController@getaccount')->name('invoice.getaccount');
    Route::get('invoice/invoiceitem/{id}', 'InvoiceController@invoiceitem')->name('invoice.invoiceitem');
    Route::post('invoice/storeitem/{id}', 'InvoiceController@storeitem')->name('invoice.storeitem');
    Route::get('invoice/items', 'InvoiceController@items')->name('invoice.items');
    Route::get('invoice/item/edit/{id}', 'InvoiceController@invoiceItemEdit')->name('invoice.item.edit');
    Route::post('invoice/item/edit/{id}', 'InvoiceController@invoiceItemUpdate')->name('invoice.item.update');
    Route::delete('invoice/items/{id}/delete', 'InvoiceController@itemsDestroy')->name('invoice.items.delete');
    Route::resource('invoice', 'InvoiceController');
    Route::get('invoice/create/{type}/{id}', 'InvoiceController@create')->name('invoice.create');
}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::get('report/leadsanalytic', 'ReportController@leadsanalytic')->name('report.leadsanalytic');
    Route::get('report/invoiceanalytic', 'ReportController@invoiceanalytic')->name('report.invoiceanalytic');
    Route::get('report/salesorderanalytic', 'ReportController@salesorderanalytic')->name('report.salesorderanalytic');
    Route::get('report/quoteanalytic', 'ReportController@quoteanalytic')->name('report.quoteanalytic');

    Route::post('report/usersrate', 'ReportController@usersrate')->name('report.usersrate');
    Route::post('report/getparent', 'ReportController@getparent')->name('report.getparent');
    Route::post('report/supportanalytic', 'ReportController@supportanalytic')->name('report.supportanalytic');

    Route::resource('report', 'ReportController');

}
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ], function (){
    Route::post('business-setting', 'SettingController@saveBusinessSettings')->name('business.setting');
    Route::post('company-setting', 'SettingController@saveCompanySettings')->name('company.setting');
    Route::post('email-setting', 'SettingController@saveEmailSettings')->name('email.setting');
    Route::post('system-setting', 'SettingController@saveSystemSettings')->name('system.setting');
    Route::post('pusher-setting', 'SettingController@savePusherSettings')->name('pusher.setting');
    Route::get('test-mail', 'SettingController@testMail')->name('test.mail');
    Route::post('test-mail', 'SettingController@testSendMail')->name('test.send.mail');
    Route::get('settings', 'SettingController@index')->name('settings');
    Route::post('payment-setting', 'SettingController@savePaymentSettings')->name('payment.setting');
    Route::post('owner-payment-setting', 'SettingController@saveOwnerPaymentSettings')->name('owner.payment.setting');
}
);
Route::group(['middleware' => ['auth', 'XSS']], function (){
    Route::get('order', 'StripePaymentController@index')->name('order.index');
    Route::get('/stripe/{code}', 'StripePaymentController@stripe')->name('stripe');
    Route::post('/stripe', 'StripePaymentController@stripePost')->name('stripe.post');
});


Route::get('profile/{id?}', 'UserController@profile')->name('profile')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('change-password/{id?}', 'UserController@updatePassword')->name('update.password');
Route::post('edit-profile/{id?}', 'UserController@editprofile')->name('update.account')->middleware(
    [
        'auth',
        'XSS',
    ]
);


Route::get('/apply-coupon', ['as' => 'apply.coupon','uses' => 'CouponController@applyCoupon'])->middleware(['auth', 'XSS']);


Route::group(['middleware' => ['auth', 'XSS']], function (){
    Route::resource('coupon', 'CouponController');
});

Route::get('/apply-coupon', ['as' => 'apply.coupon','uses' => 'CouponController@applyCoupon'])->middleware(['auth', 'XSS']);


Route::get('/change/mode',['as' => 'change.mode','uses' =>'UserController@changeMode']);


Route::post('plan-pay-with-paypal', 'PaypalController@planPayWithPaypal')->name('plan.pay.with.paypal')->middleware(['auth', 'XSS']);

Route::get('{id}/plan-get-payment-status', 'PaypalController@planGetPaymentStatus')->name('plan.get.payment.status')->middleware(['auth', 'XSS']);


// Form Builder
Route::resource('form_builder', 'FormBuilderController')->middleware(
    [
        'auth',
        'XSS',
    ]
);

// Form link base view
Route::get('/form/{code}', 'FormBuilderController@formView')->name('form.view')->middleware(['XSS']);
Route::post('/form_view_store', 'FormBuilderController@formViewStore')->name('form.view.store')->middleware(['XSS']);

// Form Field
Route::get('/form_builder/{id}/field', 'FormBuilderController@fieldCreate')->name('form.field.create')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('/form_builder/{id}/field', 'FormBuilderController@fieldStore')->name('form.field.store')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('/form_builder/{id}/field/{fid}/show', 'FormBuilderController@fieldShow')->name('form.field.show')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('/form_builder/{id}/field/{fid}/edit', 'FormBuilderController@fieldEdit')->name('form.field.edit')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('/form_builder/{id}/field/{fid}', 'FormBuilderController@fieldUpdate')->name('form.field.update')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::delete('/form_builder/{id}/field/{fid}', 'FormBuilderController@fieldDestroy')->name('form.field.destroy')->middleware(
    [
        'auth',
        'XSS',
    ]
);

// Form Response
Route::get('/form_response/{id}', 'FormBuilderController@viewResponse')->name('form.response')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::get('/response/{id}', 'FormBuilderController@responseDetail')->name('response.detail')->middleware(
    [
        'auth',
        'XSS',
    ]
);

// Form Field Bind
Route::get('/form_field/{id}', 'FormBuilderController@formFieldBind')->name('form.field.bind')->middleware(
    [
        'auth',
        'XSS',
    ]
);
Route::post('/form_field_store/{id}', 'FormBuilderController@bindStore')->name('form.bind.store')->middleware(
    [
        'auth',
        'XSS',
    ]
);
// end Form Builder

// --- DEV TOOLS (Test Runner) ---
Route::get('/dev/test-runner', 'TestRunnerController@index')->name('dev.test_runner');
Route::get('/dev/run-test', 'TestRunnerController@runTest')->name('dev.run_test');

Route::get('/dev/debug-routes', function() {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
    return response()->json($routes);
});
