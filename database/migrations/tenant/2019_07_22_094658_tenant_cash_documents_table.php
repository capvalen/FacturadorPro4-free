<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantCashDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_documents', function (Blueprint $table) {
           
            $table->increments('id'); 
            $table->unsignedInteger('cash_id');
            $table->unsignedInteger('document_id'); 
 
            $table->foreign('cash_id')->references('id')->on('cash')->onDelete('cascade'); 
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
        Schema::dropIfExists('cash_documents');                
        //
    }
}
