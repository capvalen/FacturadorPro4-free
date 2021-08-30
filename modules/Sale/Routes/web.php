<?php


use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {

            Route::prefix('sale-opportunities')->group(function() {

                Route::get('', 'SaleOpportunityController@index')->name('tenant.sale_opportunities.index')->middleware(['redirect.level']);
                Route::post('', 'SaleOpportunityController@store');

                Route::get('columns', 'SaleOpportunityController@columns');
                Route::get('records', 'SaleOpportunityController@records');
                Route::get('record/{id}', 'SaleOpportunityController@record');
                Route::get('create/{id?}', 'SaleOpportunityController@create')->name('tenant.sale_opportunities.create')->middleware(['redirect.level']);
                // Route::get('edit/{id}', 'SaleOpportunityController@edit');
                Route::get('search/customers', 'SaleOpportunityController@searchCustomers');
                Route::get('search/customer/{id}', 'SaleOpportunityController@searchCustomerById');

                Route::get('tables', 'SaleOpportunityController@tables');
                Route::get('table/{table}', 'SaleOpportunityController@table');
                Route::get('item/tables', 'SaleOpportunityController@item_tables');
                Route::post('email', 'SaleOpportunityController@email');

                Route::get('download/{external_id}/{format?}', 'SaleOpportunityController@download');
                Route::get('print/{external_id}/{format?}', 'SaleOpportunityController@toPrint');

                Route::post('uploads', 'SaleOpportunityFileController@uploadFile');
                Route::get('download-file/{filename}', 'SaleOpportunityFileController@download');

            });

            Route::prefix('payment-method-types')->group(function () {

                Route::get('/records', 'PaymentMethodTypeController@records');
                Route::get('/record/{id}', 'PaymentMethodTypeController@record');
                Route::post('', 'PaymentMethodTypeController@store');
                Route::delete('/{id}', 'PaymentMethodTypeController@destroy');

            });


            Route::prefix('contracts')->group(function () {

                Route::get('', 'ContractController@index')->name('tenant.contracts.index')->middleware(['redirect.level']);
                Route::get('/columns', 'ContractController@columns');
                Route::get('/records', 'ContractController@records');
                Route::get('/create/{id?}', 'ContractController@create')->name('tenant.contracts.create')->middleware('redirect.level');

                Route::get('/state-type/{state_type_id}/{id}', 'ContractController@updateStateType');
                Route::get('/filter', 'ContractController@filter');
                Route::get('/tables', 'ContractController@tables');
                Route::get('/table/{table}', 'ContractController@table');
                Route::post('', 'ContractController@store');
                Route::get('/record/{contract}', 'ContractController@record');
                Route::get('/voided/{id}', 'ContractController@voided');
                Route::get('/item/tables', 'ContractController@item_tables');
                Route::get('/option/tables', 'ContractController@option_tables');
                Route::get('/search/customers', 'ContractController@searchCustomers');
                Route::get('/search/customer/{id}', 'ContractController@searchCustomerById');
                Route::get('/download/{external_id}/{format?}', 'ContractController@download');
                Route::get('/print/{external_id}/{format?}', 'ContractController@toPrint');
                Route::post('/email', 'ContractController@email');
                Route::get('/record2/{contract}', 'ContractController@record2');
                Route::get('/changed/{contract}', 'ContractController@changed');
                Route::get('/generate-quotation/{quotation}', 'ContractController@generateContract')->middleware(['redirect.level']);

            });

        });


        Route::prefix('production-orders')->group(function () {

            Route::get('', 'ProductionOrderController@index')->name('tenant.production_orders.index')->middleware(['redirect.level']);
            Route::get('/columns', 'ProductionOrderController@columns');
            Route::get('/records', 'ProductionOrderController@records');

        });

        Route::prefix('technical-services')->group(function () {

            Route::get('', 'TechnicalServiceController@index')->name('tenant.technical_services.index')->middleware(['redirect.level']);
            Route::get('/columns', 'TechnicalServiceController@columns');
            Route::get('/records', 'TechnicalServiceController@records');
            Route::get('/tables', 'TechnicalServiceController@tables');
            Route::post('', 'TechnicalServiceController@store');
            Route::get('/record/{contract}', 'TechnicalServiceController@record');
            Route::get('/search/customers', 'TechnicalServiceController@searchCustomers');
            Route::get('/search/customer/{id}', 'TechnicalServiceController@searchCustomerById');
            Route::get('/download/{id}/{format?}', 'TechnicalServiceController@download');
            Route::get('/print/{id}/{format?}', 'TechnicalServiceController@toPrint');
            Route::delete('/{id}', 'TechnicalServiceController@destroy');

        });


        Route::prefix('user-commissions')->group(function () {

            Route::get('', 'UserCommissionController@index')->name('tenant.user_commissions.index')->middleware(['redirect.level']);
            Route::get('/columns', 'UserCommissionController@columns');
            Route::get('/records', 'UserCommissionController@records');
            Route::get('/tables', 'UserCommissionController@tables');
            Route::post('', 'UserCommissionController@store');
            Route::get('/record/{id}', 'UserCommissionController@record');
            Route::delete('/{id}', 'UserCommissionController@destroy');

        });

        Route::prefix('quotation_payments')->group(function () {

            Route::get('/records/{quotation}', 'QuotationPaymentController@records');
            Route::get('/document/{quotation}', 'QuotationPaymentController@document');
            Route::get('/tables', 'QuotationPaymentController@tables');
            Route::post('', 'QuotationPaymentController@store');
            Route::delete('/{quotation_payment}', 'QuotationPaymentController@destroy');

        });

        Route::prefix('technical-service-payments')->group(function () {

            Route::get('/records/{record}', 'TechnicalServicePaymentController@records');
            Route::get('/document/{record}', 'TechnicalServicePaymentController@document');
            Route::get('/tables', 'TechnicalServicePaymentController@tables');
            Route::post('', 'TechnicalServicePaymentController@store');
            Route::delete('/{record_payment}', 'TechnicalServicePaymentController@destroy');

        });

        Route::prefix('generate-document')->group(function () {
            Route::get('/record/{table}/{record}', 'GenerateDocumentController@record');
            Route::get('/tables', 'GenerateDocumentController@tables');
            Route::post('/', 'GenerateDocumentController@store');
            Route::post('/customers', 'GenerateDocumentController@customers');
//            Route::post('/store_item', 'GenerateDocumentController@storeItem');
        });
    });
}
