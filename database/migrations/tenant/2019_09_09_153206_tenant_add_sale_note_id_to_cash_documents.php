<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSaleNoteIdToCashDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_documents', function (Blueprint $table) {

            $table->unsignedInteger('document_id')->nullable()->change(); 

            $table->unsignedInteger('sale_note_id')->nullable()->after('document_id');
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
        Schema::table('cash_documents', function (Blueprint $table) {

            $table->unsignedInteger('document_id')->change(); 

            $table->dropForeign(['sale_note_id']);
            $table->dropColumn('sale_note_id');  

        });
    }
}
