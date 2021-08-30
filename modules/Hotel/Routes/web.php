<?php

use Illuminate\Support\Facades\Route;

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($hostname) {
	Route::domain($hostname->fqdn)->group(function () {
		Route::middleware(['auth', 'redirect.module', 'locked.tenant'])
			->prefix('hotels')
			->group(function () {
				// Tarifas
				Route::get('rates', 'HotelRateController@index');
				Route::post('rates/store', 'HotelRateController@store');
				Route::put('rates/{id}/update', 'HotelRateController@update');
				Route::delete('rates/{id}/delete', 'HotelRateController@destroy');
				// Categorías
				Route::get('categories', 'HotelCategoryController@index');
				Route::post('categories/store', 'HotelCategoryController@store');
				Route::put('categories/{id}/update', 'HotelCategoryController@update');
				Route::delete('categories/{id}/delete', 'HotelCategoryController@destroy');
				// Pisos
				Route::get('floors', 'HotelFloorController@index');
				Route::post('floors/store', 'HotelFloorController@store');
				Route::put('floors/{id}/update', 'HotelFloorController@update');
				Route::delete('floors/{id}/delete', 'HotelFloorController@destroy');
				// Habitaciones
				Route::get('rooms', 'HotelRoomController@index');
				Route::post('rooms/store', 'HotelRoomController@store');
				Route::put('rooms/{id}/update', 'HotelRoomController@update');
				Route::delete('rooms/{id}/delete', 'HotelRoomController@destroy');
				Route::post('rooms/{id}/change-status', 'HotelRoomController@changeRoomStatus');

				Route::get('rooms/tables', 'HotelRoomController@tables');

				Route::get('rooms/{id}/rates', 'HotelRoomController@myRates');
				Route::post('rooms/{id}/rates/store', 'HotelRoomController@addRateToRoom');
				Route::delete('rooms/{id}/rates/{rateId}/delete', 'HotelRoomController@deleteRoomRate');

				Route::get('reception', 'HotelReceptionController@index');
				Route::get('reception/tables', 'HotelRentController@tables');
				Route::get('reception/tables/customers', 'HotelRentController@searchCustomers');
				Route::get('reception/{roomId}/rent', 'HotelRentController@rent');
				Route::post('reception/{roomId}/rent/store', 'HotelRentController@store');
				Route::get('reception/{id}/rent/products', 'HotelRentController@showFormAddProduct');
				Route::post('reception/{id}/rent/products/store', 'HotelRentController@addProductsToRoom');
				Route::get('reception/{id}/rent/checkout', 'HotelRentController@showFormChekout');
				Route::post('reception/{id}/rent/finalized', 'HotelRentController@finalizeRent');
			});
	});
}
