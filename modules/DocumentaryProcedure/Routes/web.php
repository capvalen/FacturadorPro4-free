<?php

use Illuminate\Support\Facades\Route;

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
	Route::domain($hostname->fqdn)->group(function () {
		Route::middleware(['auth', 'redirect.module', 'locked.tenant'])->prefix('documentary-procedure')->group(function () {
			Route::get('offices', 'DocumentaryOfficeController@index')->name('documentary.offices');
			Route::post('offices/store', 'DocumentaryOfficeController@store');
			Route::put('offices/{id}/update', 'DocumentaryOfficeController@update');
			Route::delete('offices/{id}/delete', 'DocumentaryOfficeController@destroy');

			Route::get('processes', 'DocumentaryProcessController@index')->name('documentary.processes');
			Route::post('processes/store', 'DocumentaryProcessController@store');
			Route::put('processes/{id}/update', 'DocumentaryProcessController@update');
			Route::delete('processes/{id}/delete', 'DocumentaryProcessController@destroy');

			Route::get('documents', 'DocumentaryDocumentController@index')->name('documentary.documents');
			Route::post('documents/store', 'DocumentaryDocumentController@store');
			Route::put('documents/{id}/update', 'DocumentaryDocumentController@update');
			Route::delete('documents/{id}/delete', 'DocumentaryDocumentController@destroy');

			Route::get('actions', 'DocumentaryActionController@index')->name('documentary.actions');
			Route::post('actions/store', 'DocumentaryActionController@store');
			Route::put('actions/{id}/update', 'DocumentaryActionController@update');
			Route::delete('actions/{id}/delete', 'DocumentaryActionController@destroy');

            Route::get('files', 'DocumentaryFileController@index')->name('documentary.files');
            Route::post('files/store', 'DocumentaryFileController@store');
            Route::post('files/{id}/update', 'DocumentaryFileController@update');
            Route::delete('files/{id}/delete', 'DocumentaryFileController@destroy');
            Route::get('files/tables', 'DocumentaryFileController@tables');
            Route::get('files/create', 'DocumentaryFileController@create');
            Route::get('files/document-number', 'DocumentaryFileController@getDocumentNumber');
            Route::post('files/{id}/add-office', 'DocumentaryFileController@addOffice');
		});
	});
}
