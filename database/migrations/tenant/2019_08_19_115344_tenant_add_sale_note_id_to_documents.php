<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSaleNoteIdToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('sale_note_id')->nullable()->after('quotation_id');
            $table->foreign('sale_note_id')->references('id')->on('sale_notes');
        });
    }

    
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['sale_note_id']);
            $table->dropColumn('sale_note_id');
        });
    }
}
