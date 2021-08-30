<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantSaleOpportunityFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_opportunity_files', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('sale_opportunity_id'); 
            $table->string('filename');
            $table->foreign('sale_opportunity_id')->references('id')->on('sale_opportunities')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_opportunity_files');
        
    }
}
