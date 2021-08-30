<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifyDocumentIdToCashDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_documents', function (Blueprint $table) {

            $table->dropForeign('cash_documents_document_id_foreign');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade'); 
            //
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

            $table->dropForeign('cash_documents_document_id_foreign');
            $table->foreign('document_id')->references('id')->on('documents'); 
            //
        });
    }
}
