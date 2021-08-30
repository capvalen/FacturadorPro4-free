<?php

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

Route::prefix('padron')->group(function() {
    //Route::get('/', 'PadronController@index');
    Route::get('consulta/{ruc}', 'PadronController@consulta');
    Route::get('charges_data', 'PadronController@charges_data');
    Route::get('list_history', 'PadronController@list_history');
    Route::get('demo', 'PadronController@demo');

});
