<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotel_rents', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('customer_id');
			$table->text('customer');
			$table->string('notes', 250)->nullable();
			$table->integer('towels')->default(1);
			$table->unsignedInteger('hotel_room_id');
			$table->integer('duration')->default(1);
			$table->integer('quantity_persons')->default(1);
			$table->string('payment_type', 10)->nullable();
			$table->string('payment_number_operation', 20)->nullable();
			$table->string('payment_status', 10);
			$table->date('output_date');
			$table->string('output_time', 8);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hotel_rents');
	}
}
