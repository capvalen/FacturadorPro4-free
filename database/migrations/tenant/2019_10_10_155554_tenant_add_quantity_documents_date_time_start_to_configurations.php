<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddQuantityDocumentsDateTimeStartToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {

            $table->integer('quantity_documents')->after('locked_emission');
            $table->datetime('date_time_start')->nullable()->after('locked_emission');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {

            $table->dropColumn('quantity_documents');
            $table->dropColumn('date_time_start');

        });
    }
}
