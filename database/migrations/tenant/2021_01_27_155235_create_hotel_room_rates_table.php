<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRoomRatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotel_room_rates', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('hotel_room_id');
			$table->unsignedInteger('hotel_rate_id');
			$table->double('price')->default(0);
			$table->timestamps();

			$table->foreign('hotel_room_id')->references('id')->on('hotel_rooms')->onDelete('cascade');
			$table->foreign('hotel_rate_id')->references('id')->on('hotel_rates')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hotel_room_rates');
	}
}
