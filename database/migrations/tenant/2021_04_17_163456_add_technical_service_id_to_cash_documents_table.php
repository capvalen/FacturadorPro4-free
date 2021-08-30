<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTechnicalServiceIdToCashDocumentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cash_documents', function (Blueprint $table) {
            $table->unsignedInteger('technical_service_id')->nullable()->after('sale_note_id');
            $table->foreign('technical_service_id')->references('id')->on('technical_services')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cash_documents', function (Blueprint $table) {
            $table->dropForeign(['technical_service_id']);
            $table->dropColumn('technical_service_id');
        });
	}
}
