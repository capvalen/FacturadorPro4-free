<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function () {

            // Route::redirect('/', '/dashboard');

            Route::prefix('expenses')->group(function () {

                Route::get('', 'ExpenseController@index')->name('tenant.expenses.index');
                Route::get('columns', 'ExpenseController@columns');
                Route::get('records', 'ExpenseController@records');
                Route::get('records/expense-payments/{expense}', 'ExpenseController@recordsExpensePayments');
                Route::get('create/{id?}', 'ExpenseController@create')->name('tenant.expenses.create');
                Route::get('tables', 'ExpenseController@tables');
                Route::get('table/{table}', 'ExpenseController@table');
                Route::post('', 'ExpenseController@store');
                Route::get('record/{expense}', 'ExpenseController@record');
                Route::get('{record}/voided', 'ExpenseController@voided');

            });

            
            Route::prefix('expense-payments')->group(function () {

                Route::get('/records/{expense_id}', 'ExpensePaymentController@records');
                Route::get('/expense/{expense_id}', 'ExpensePaymentController@expense');
                Route::get('/tables', 'ExpensePaymentController@tables');
                Route::post('', 'ExpensePaymentController@store');
                Route::delete('/{expense_payment}', 'ExpensePaymentController@destroy');

            });

            Route::prefix('expense-types')->group(function () {

                Route::get('/records', 'ExpenseTypeController@records');
                Route::get('/record/{id}', 'ExpenseTypeController@record');
                Route::post('', 'ExpenseTypeController@store');
                Route::delete('/{id}', 'ExpenseTypeController@destroy');

            });

            Route::prefix('expense-reasons')->group(function () {

                Route::get('/records', 'ExpenseReasonController@records');
                Route::get('/record/{id}', 'ExpenseReasonController@record');
                Route::post('', 'ExpenseReasonController@store');
                Route::delete('/{id}', 'ExpenseReasonController@destroy');

            });

            Route::prefix('expense-method-types')->group(function () {

                Route::get('/records', 'ExpenseMethodTypeController@records');
                Route::get('/record/{id}', 'ExpenseMethodTypeController@record');
                Route::post('', 'ExpenseMethodTypeController@store');
                Route::delete('/{id}', 'ExpenseMethodTypeController@destroy');

            });

        });
    });
}
