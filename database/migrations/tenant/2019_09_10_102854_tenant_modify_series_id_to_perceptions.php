<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifySeriesIdToPerceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perceptions', function (Blueprint $table) {
            
            $table->dropForeign(['series_id']);
            $table->dropColumn('series_id');
            $table->char('series', 4)->after('document_type_id')->index();
            $table->index('number');	

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perceptions', function (Blueprint $table) {
            
            $table->unsignedInteger('series_id');
            $table->foreign('series_id')->references('id')->on('series');

            $table->dropIndex(['series']);
            $table->dropColumn('series');

            $table->dropIndex(['number']);

        });
    }
}
