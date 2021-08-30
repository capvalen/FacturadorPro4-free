<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantVoidedDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voided_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voided_id');
            $table->unsignedInteger('document_id');
            $table->string('description');

            $table->foreign('voided_id')->references('id')->on('voided')->onDelete('cascade');
            $table->foreign('document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voided_documents');
    }
}
