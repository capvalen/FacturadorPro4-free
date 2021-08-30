<?php

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->group(function () {


            Route::prefix('reports')->group(function () {

                Route::get('data-table/persons/{type}', 'ReportController@dataTablePerson');
                Route::get('data-table/items', 'ReportController@dataTableItem');

                Route::get('purchases', 'ReportPurchaseController@index')->name('tenant.reports.purchases.index');
                Route::get('purchases/pdf', 'ReportPurchaseController@pdf')->name('tenant.reports.purchases.pdf');
                Route::get('purchases/excel', 'ReportPurchaseController@excel')->name('tenant.reports.purchases.excel');
                Route::get('purchases/filter', 'ReportPurchaseController@filter')->name('tenant.reports.purchases.filter');
                Route::get('purchases/records', 'ReportPurchaseController@records')->name('tenant.reports.purchases.records');

                Route::get('sales', 'ReportDocumentController@index')->name('tenant.reports.sales.index')->middleware('tenant.internal.mode');
                Route::get('sales/pdf', 'ReportDocumentController@pdf')->name('tenant.reports.sales.pdf');
                Route::get('sales/excel', 'ReportDocumentController@excel')->name('tenant.reports.sales.excel');
                Route::get('sales/filter', 'ReportDocumentController@filter')->name('tenant.reports.sales.filter');
                Route::get('sales/records', 'ReportDocumentController@records')->name('tenant.reports.sales.records');

                Route::get('sale-notes', 'ReportSaleNoteController@index')->name('tenant.reports.sale_notes.index');
                Route::get('sale-notes/pdf', 'ReportSaleNoteController@pdf')->name('tenant.reports.sale_notes.pdf');
                Route::get('sale-notes/excel', 'ReportSaleNoteController@excel')->name('tenant.reports.sale_notes.excel');
                Route::get('sale-notes/filter', 'ReportSaleNoteController@filter')->name('tenant.reports.sale_notes.filter');
                Route::get('sale-notes/records', 'ReportSaleNoteController@records')->name('tenant.reports.sale_notes.records');

                Route::get('quotations', 'ReportQuotationController@index')->name('tenant.reports.quotations.index');
                Route::get('quotations/pdf', 'ReportQuotationController@pdf')->name('tenant.reports.quotations.pdf');
                Route::get('quotations/excel', 'ReportQuotationController@excel')->name('tenant.reports.quotations.excel');
                Route::get('quotations/filter', 'ReportQuotationController@filter')->name('tenant.reports.quotations.filter');
                Route::get('quotations/records', 'ReportQuotationController@records')->name('tenant.reports.quotations.records');

                Route::get('cash', 'ReportCashController@index')->name('tenant.reports.cash.index');
                Route::get('cash/pdf', 'ReportCashController@pdf')->name('tenant.reports.cash.pdf');
                Route::get('cash/excel', 'ReportCashController@excel')->name('tenant.reports.cash.excel');
                Route::get('cash/filter', 'ReportCashController@filter')->name('tenant.reports.cash.filter');
                Route::get('cash/records', 'ReportCashController@records')->name('tenant.reports.cash.records');



                Route::get('document-hotels', 'ReportDocumentHotelController@index')->name('tenant.reports.document_hotels.index');
                Route::get('document-hotels/pdf', 'ReportDocumentHotelController@pdf')->name('tenant.reports.document_hotels.pdf');
                Route::get('document-hotels/excel', 'ReportDocumentHotelController@excel')->name('tenant.reports.document_hotels.excel');
                Route::get('document-hotels/filter', 'ReportDocumentHotelController@filter')->name('tenant.reports.document_hotels.filter');
                Route::get('document-hotels/records', 'ReportDocumentHotelController@records')->name('tenant.reports.document_hotels.records');



                Route::get('commercial-analysis', 'ReportCommercialAnalysisController@index')->name('tenant.reports.commercial_analysis.index');
                Route::get('commercial-analysis/pdf', 'ReportCommercialAnalysisController@pdf')->name('tenant.reports.commercial_analysis.pdf');
                Route::get('commercial-analysis/excel', 'ReportCommercialAnalysisController@excel')->name('tenant.reports.commercial_analysis.excel');
                Route::get('commercial-analysis/filter', 'ReportCommercialAnalysisController@filter')->name('tenant.reports.commercial_analysis.filter');
                Route::get('commercial-analysis/records', 'ReportCommercialAnalysisController@records')->name('tenant.reports.commercial_analysis.records');
                Route::get('commercial-analysis/data_table', 'ReportCommercialAnalysisController@data_table');
                Route::get('commercial-analysis/columns', 'ReportCommercialAnalysisController@columns');
                Route::get('no_paid/excel', 'ReportUnpaidController@excel')->name('tenant.reports.no_paid.excel');

                Route::get('document-detractions', 'ReportDocumentDetractionController@index')->name('tenant.reports.document_detractions.index');
                Route::get('document-detractions/pdf', 'ReportDocumentDetractionController@pdf')->name('tenant.reports.document_detractions.pdf');
                Route::get('document-detractions/excel', 'ReportDocumentDetractionController@excel')->name('tenant.reports.document_detractions.excel');
                Route::get('document-detractions/filter', 'ReportDocumentDetractionController@filter')->name('tenant.reports.document_detractions.filter');
                Route::get('document-detractions/records', 'ReportDocumentDetractionController@records')->name('tenant.reports.document_hotels.records');


                Route::get('commissions', 'ReportCommissionController@index')->name('tenant.reports.commissions.index');
                Route::get('commissions/pdf', 'ReportCommissionController@pdf')->name('tenant.reports.commissions.pdf');
                Route::get('commissions/excel', 'ReportCommissionController@excel')->name('tenant.reports.commissions.excel');
                Route::get('commissions/filter', 'ReportCommissionController@filter')->name('tenant.reports.commissions.filter');
                Route::get('commissions/records', 'ReportCommissionController@records')->name('tenant.reports.commissions.records');

                Route::get('customers', 'ReportCustomerController@index')->name('tenant.reports.customers.index');
                Route::get('customers/excel', 'ReportCustomerController@excel')->name('tenant.reports.customers.excel');
                Route::get('customers/filter', 'ReportCustomerController@filter')->name('tenant.reports.customers.filter');
                Route::get('customers/records', 'ReportCustomerController@records')->name('tenant.reports.customers.records');

                Route::get('items', 'ReportItemController@index')->name('tenant.reports.items.index');
                Route::get('items/excel', 'ReportItemController@excel')->name('tenant.reports.items.excel');
                Route::get('items/filter', 'ReportItemController@filter')->name('tenant.reports.items.filter');
                Route::get('items/records', 'ReportItemController@records')->name('tenant.reports.items.records');

                Route::get('order-notes-consolidated', 'ReportOrderNoteConsolidatedController@index')->name('tenant.reports.order_notes_consolidated.index');
                Route::get('order-notes-consolidated/pdf', 'ReportOrderNoteConsolidatedController@pdf');
                // Route::get('order-notes-consolidated/excel', 'ReportOrderNoteConsolidatedController@excel');
                Route::get('order-notes-consolidated/filter', 'ReportOrderNoteConsolidatedController@filter');
                Route::get('order-notes-consolidated/records', 'ReportOrderNoteConsolidatedController@records');

                Route::get('general-items', 'ReportGeneralItemController@index')->name('tenant.reports.general_items.index');
                Route::get('general-items/excel', 'ReportGeneralItemController@excel');
                Route::get('general-items/pdf', 'ReportGeneralItemController@pdf');
                Route::get('general-items/filter', 'ReportGeneralItemController@filter');
                Route::get('general-items/records', 'ReportGeneralItemController@records');


                Route::get('order-notes-general', 'ReportOrderNoteGeneralController@index')->name('tenant.reports.order_notes_general.index');
                Route::get('order-notes-general/pdf', 'ReportOrderNoteGeneralController@pdf');
                Route::get('order-notes-general/filter', 'ReportOrderNoteGeneralController@filter');
                Route::get('order-notes-general/records', 'ReportOrderNoteGeneralController@records');

                Route::get('sales-consolidated', 'ReportSaleConsolidatedController@index')->name('tenant.reports.sales_consolidated.index');
                Route::get('sales-consolidated/pdf', 'ReportSaleConsolidatedController@pdf');
                Route::get('sales-consolidated/excel', 'ReportSaleConsolidatedController@excel');
                Route::get('sales-consolidated/filter', 'ReportSaleConsolidatedController@filter');
                Route::get('sales-consolidated/records', 'ReportSaleConsolidatedController@records');
                Route::get('sales-consolidated/totals-by-item', 'ReportSaleConsolidatedController@totalsByItem');
                Route::get('sales-consolidated/pdf-totals', 'ReportSaleConsolidatedController@pdfTotals');
                Route::get('sales-consolidated/excel-totals', 'ReportSaleConsolidatedController@excelTotals');


                Route::prefix('user-commissions')->group(function () {

                    Route::get('', 'ReportUserCommissionController@index')->name('tenant.reports.user_commissions.index');
                    Route::get('/pdf', 'ReportUserCommissionController@pdf')->name('tenant.reports.user_commissions.pdf');
                    Route::get('/excel', 'ReportUserCommissionController@excel')->name('tenant.reports.user_commissions.excel');
                    Route::get('/filter', 'ReportUserCommissionController@filter')->name('tenant.reports.user_commissions.filter');
                    Route::get('/records', 'ReportUserCommissionController@records')->name('tenant.reports.user_commissions.records');

                });


                Route::prefix('fixed-asset-purchases')->group(function () {

                    Route::get('', 'ReportFixedAssetPurchaseController@index')->name('tenant.reports.fixed-asset-purchases.index');
                    Route::get('pdf', 'ReportFixedAssetPurchaseController@pdf');
                    Route::get('excel', 'ReportFixedAssetPurchaseController@excel');
                    Route::get('filter', 'ReportFixedAssetPurchaseController@filter');
                    Route::get('records', 'ReportFixedAssetPurchaseController@records');
    
                });

                Route::prefix('massive-downloads')->group(function () {

                    Route::get('', 'ReportMassiveDownloadController@index')->name('tenant.reports.massive-downloads.index');
                    Route::get('filter', 'ReportMassiveDownloadController@filter');
                    Route::get('pdf', 'ReportMassiveDownloadController@pdf');
                    Route::get('records', 'ReportMassiveDownloadController@records');
    
                });

            });

            Route::get('cash/report/income-summary/{cash}', 'ReportIncomeSummaryController@pdf')->name('tenant.reports.income_summary.pdf');


        });
    });
}
