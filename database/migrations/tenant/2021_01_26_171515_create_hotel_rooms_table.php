<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRoomsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotel_rooms', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('hotel_category_id');
			$table->unsignedInteger('hotel_floor_id');
			$table->string('name', 25);
			$table->string('description', 250)->nullable();
			/**
			 * Estados de la habitación: disponible, en mantenimiento, en limpieza, etc
			 */
			$table->string('status')->default('DISPONIBLE');
			$table->boolean('active')->default(true);
			$table->timestamps();

			$table->foreign('hotel_category_id')->references('id')->on('hotel_categories')->onDelete('cascade');
			$table->foreign('hotel_floor_id')->references('id')->on('hotel_floors')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hotel_rooms');
	}
}
