<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsToDocumentHotels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_hotels', function (Blueprint $table) {
            $table->string('room_type')->nullable()->after('time_exit');
            $table->string('ocupation')->nullable()->after('time_exit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_hotels', function (Blueprint $table) {
            $table->dropColumn('room_type');
            $table->dropColumn('ocupation');
        });
    }
}
