<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function() {

            Route::prefix('account')->group(function () {
                Route::get('/', 'AccountController@index')->name('tenant.account.index');
                Route::get('download', 'AccountController@download');
                Route::get('format', 'FormatController@index')->name('tenant.account_format.index');
                Route::get('format/download', 'FormatController@download');


                Route::get('summary-report', 'SummaryReportController@index')->name('tenant.account_summary_report.index');
                Route::get('summary-report/records', 'SummaryReportController@records');
                Route::get('summary-report/format/download', 'SummaryReportController@download');

            });

            Route::prefix('company_accounts')->group(function () {
                Route::get('create', 'CompanyAccountController@create')->name('tenant.company_accounts.create');
                Route::get('record', 'CompanyAccountController@record');
                Route::post('', 'CompanyAccountController@store');
            });


        });
    });
} 
else {

    Route::domain(env('APP_URL_BASE'))->group(function() {
 
        Route::middleware('auth:admin')->group(function() {

            Route::prefix('accounting')->group(function () {

                Route::get('', 'System\AccountingController@index')->name('system.accounting.index');
                Route::get('records', 'System\AccountingController@records');
                Route::get('download', 'System\AccountingController@download');

            });
            

        });
    });

}

