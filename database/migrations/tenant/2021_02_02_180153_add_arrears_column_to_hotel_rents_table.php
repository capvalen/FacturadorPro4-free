<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArrearsColumnToHotelRentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotel_rents', function (Blueprint $table) {
			$table->integer('arrears')->default(0)->after('output_time');
			$table->string('status', 10)->default('INICIADO')->after('arrears');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hotel_rents', function (Blueprint $table) {
			$table->dropColumn('arrears');
			$table->dropColumn('status');
		});
	}
}
