<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRentItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotel_rent_items', function (Blueprint $table) {
			$table->increments('id');
			$table->string('type', 3);
			$table->unsignedInteger('hotel_rent_id');
			$table->unsignedInteger('item_id');
			$table->text('item')->nullable();
			$table->string('payment_status');
			$table->timestamps();

			$table->foreign('hotel_rent_id')->references('id')->on('hotel_rents')->onDelete('cascade');
			$table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hotel_rent_items');
	}
}
