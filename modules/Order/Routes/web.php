<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {

        Route::get('order-forms/print/{external_id}/{format?}', 'OrderFormController@toPrint');
        Route::get('order-notes/print/{external_id}/{format?}', 'OrderNoteController@toPrint');

        Route::get('reporte-pedidos', 'OrderNoteController@prueba');
        Route::get('reporte-pedidos/table', 'OrderNoteController@pruebaTable')->name('tenant.order_notes.pedidos');
        Route::get('reporte-pedidos/tableReportVendedor', 'OrderNoteController@reporteVendedorXProducto')->name('tenant.order_notes.reporte_vendedor');
        Route::get('reporte-pedidos/carteraClientes', 'OrderNoteController@carteraClientes')->name('tenant.order_notes.cartera_clientes');
        Route::get('reporte-pedidos/carteraClientesReal', 'OrderNoteController@carteraClientesReal')->name('tenant.order_notes.cartera_clientes_real');
        Route::get('reporte-pedidos/clientesVisitados', 'OrderNoteController@clientesVisitadosXDia')->name('tenant.order_notes.clientes_visitados');
        Route::get('reporte-pedidos/pedidosYVendedor', 'OrderNoteController@pedidosYVendedor')->name('tenant.order_notes.pedidos_y_vendedor');
        
        Route::middleware(['auth', 'locked.tenant'])->group(function () {

            Route::prefix('order-notes')->group(function () {

                Route::get('/', 'OrderNoteController@index')->name('tenant.order_notes.index')->middleware(['redirect.level']);
                Route::get('columns', 'OrderNoteController@columns');
                Route::get('records', 'OrderNoteController@records');
                Route::get('create', 'OrderNoteController@create')->name('tenant.order_notes.create')->middleware(['redirect.level']);
                Route::get('edit/{id}', 'OrderNoteController@edit')->middleware(['redirect.level']);

                Route::get('tables', 'OrderNoteController@tables');
                Route::get('table/{table}', 'OrderNoteController@table');
                Route::post('/', 'OrderNoteController@store');
                Route::post('update', 'OrderNoteController@update');
                Route::get('record/{quotation}', 'OrderNoteController@record');
                Route::get('voided/{id}', 'OrderNoteController@voided');
                Route::get('item/tables', 'OrderNoteController@item_tables');
                Route::get('option/tables', 'OrderNoteController@option_tables');
                Route::get('search/customers', 'OrderNoteController@searchCustomers');
                Route::get('search/customer/{id}', 'OrderNoteController@searchCustomerById');
                Route::get('download/{external_id}/{format?}', 'OrderNoteController@download');
                // Route::get('print/{external_id}/{format?}', 'OrderNoteController@toPrint');
                Route::post('email', 'OrderNoteController@email');
                Route::post('duplicate', 'OrderNoteController@duplicate');
                Route::get('record2/{quotation}', 'OrderNoteController@record2');
                Route::delete('destroy_order_note_item/{order_note_item}', 'OrderNoteController@destroy_order_note_item');
                Route::get('documents', 'OrderNoteController@documents');
                Route::post('documents', 'OrderNoteController@generateDocuments');
                Route::get('document_tables', 'OrderNoteController@document_tables');

            });

            Route::prefix('order-forms')->group(function () {

                Route::get('/', 'OrderFormController@index')->name('tenant.order_forms.index');
                Route::get('columns', 'OrderFormController@columns');
                Route::get('records', 'OrderFormController@records');
                Route::get('create/{id?}', 'OrderFormController@create')->name('tenant.order_forms.create');

                Route::post('tables', 'OrderFormController@tables');
                Route::get('table/{table}', 'OrderFormController@table');
                Route::post('/', 'OrderFormController@store');
                Route::get('record/{id}', 'OrderFormController@record');
                Route::get('item/tables', 'OrderFormController@item_tables');
                Route::get('option/tables', 'OrderFormController@option_tables');
                Route::get('search/customers', 'OrderFormController@searchCustomers');
                Route::get('search/customer/{id}', 'OrderFormController@searchCustomerById');
                Route::get('download/{external_id}/{format?}', 'OrderFormController@download');
                Route::post('email', 'OrderFormController@email');

                Route::get('dispatch-create/{id?}', 'OrderFormController@dispatchCreate');

            });

            Route::prefix('drivers')->group(function () {

                Route::get('/', 'DriverController@index')->name('tenant.order_forms.drivers.index');
                Route::get('columns', 'DriverController@columns');
                Route::get('records', 'DriverController@records');
                Route::get('record/{id}', 'DriverController@record');
                Route::get('tables', 'DriverController@tables');
                Route::post('/', 'DriverController@store');
                Route::delete('/{id}', 'DriverController@destroy');

            });

            Route::prefix('dispatchers')->group(function () {

                Route::get('/', 'DispatcherController@index')->name('tenant.order_forms.dispatchers.index');
                Route::get('columns', 'DispatcherController@columns');
                Route::get('records', 'DispatcherController@records');
                Route::get('record/{id}', 'DispatcherController@record');
                Route::get('tables', 'DispatcherController@tables');
                Route::post('/', 'DispatcherController@store');
                Route::delete('/{id}', 'DispatcherController@destroy');

            });

        });
    });
}
