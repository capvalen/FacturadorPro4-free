<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);
if ($hostname) {
    Route::domain($hostname->fqdn)->group(function() {

        Route::middleware(['auth:api', 'locked.tenant'])->group(function() {

            Route::post('purchase/email', 'Api\PurchaseController@email');
            
            Route::prefix('purchases')->group(function () {
                Route::get('records', 'Api\PurchaseController@records');
                Route::get('tables', 'Api\PurchaseController@tables');
                Route::get('suppliers', 'Api\PurchaseController@suppliers');
                Route::get('search-suppliers', 'Api\PurchaseController@searchSuppliers');
                Route::get('item-tables', 'Api\PurchaseController@item_tables');
                Route::get('table/{table}', 'Api\PurchaseController@table');
                Route::post('', 'Api\PurchaseController@store');
            });

        }); 

    });
}
