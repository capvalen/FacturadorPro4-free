<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnItemIdToHotelRoomsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotel_rooms', function (Blueprint $table) {
			$table->unsignedInteger('item_id')->nullable()->after('id');

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
		Schema::table('hotel_rooms', function (Blueprint $table) {
			$table->dropForeign('hotel_rooms_item_id_foreign');
			$table->dropColumn('item_id');
		});
	}
}
