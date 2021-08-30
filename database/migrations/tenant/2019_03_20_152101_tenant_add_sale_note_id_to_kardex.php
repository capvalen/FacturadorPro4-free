<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSaleNoteIdToKardex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kardex', function (Blueprint $table) {

            $table->unsignedInteger('sale_note_id')->nullable()->after('purchase_id');
            $table->foreign('sale_note_id')->references('id')->on('sale_notes')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kardex', function (Blueprint $table) {

            $table->dropForeign(['sale_note_id']);
            $table->dropColumn('sale_note_id');      
                   
        });
    }
}
