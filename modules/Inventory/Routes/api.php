<?php

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($hostname) {
    Route::domain($hostname->fqdn)->group(function () {
        
        Route::middleware(['auth:api', 'redirect.module', 'locked.tenant'])->group(function() {


            Route::prefix('inventory')->group(function () {

                Route::post('/transaction', 'Api\InventoryController@store_transaction');

            });


        });
    });
}
