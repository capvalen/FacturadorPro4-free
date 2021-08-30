<?php


$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {

            Route::prefix('documents/not-sent')->group(function() {
                Route::get('', 'DocumentController@index')->name('tenant.documents.not_sent')->middleware('redirect.level','tenant.internal.mode');
                Route::get('records', 'DocumentController@records');
                Route::get('data_table', 'DocumentController@data_table');

            });

            Route::prefix('documents')->group(function() {
                Route::post('pay-constancy/upload', 'DocumentController@upload');
                Route::post('pay-constancy/save', 'DocumentController@savePayConstancy');
                Route::get('detraction/tables', 'DocumentController@detractionTables');
                Route::get('data-table/customers', 'DocumentController@dataTableCustomers');
                Route::get('prepayments/{type}', 'DocumentController@prepayments');
                Route::get('search-items', 'DocumentController@searchItems');
                Route::get('search/item/{item}', 'DocumentController@searchItemById');
                Route::get('consult_cdr/{document}', 'DocumentController@consultCdr');

                Route::get('item-lots', 'DocumentController@searchLots');
                Route::get('regularize-lots/{document_item_id}', 'DocumentController@regularizeLots');

            });

            Route::prefix('series-configurations')->group(function() {

                Route::get('', 'SeriesConfigurationController@index')->name('tenant.series_configurations.index');
                Route::get('records', 'SeriesConfigurationController@records');
                Route::get('tables', 'SeriesConfigurationController@tables');
                Route::post('', 'SeriesConfigurationController@store');
                Route::delete('{record}', 'SeriesConfigurationController@destroy');

            });

            Route::prefix('reports/validate-documents')->group(function() {

                Route::get('', 'ValidateDocumentController@index')->name('tenant.validate_documents.index')->middleware('tenant.internal.mode');
                Route::get('records', 'ValidateDocumentController@records');
                Route::get('data_table', 'ValidateDocumentController@data_table');
                Route::post('regularize', 'ValidateDocumentController@regularize');

            });

            Route::prefix('documents/regularize-shipping')->group(function() {
                Route::get('', 'DocumentRegularizeShippingController@index')->name('tenant.documents.regularize_shipping');
                Route::get('records', 'DocumentRegularizeShippingController@records');
                Route::get('data_table', 'DocumentRegularizeShippingController@data_table');

            });
        });
    });
}
