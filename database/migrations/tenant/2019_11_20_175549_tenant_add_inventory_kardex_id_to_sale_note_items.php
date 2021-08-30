<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddInventoryKardexIdToSaleNoteItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_note_items', function (Blueprint $table) {
            $table->unsignedInteger('inventory_kardex_id')->nullable();
            $table->foreign('inventory_kardex_id')->references('id')->on('inventory_kardex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_note_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_kardex_id']);
            $table->dropColumn('inventory_kardex_id');
        });
    }
}
