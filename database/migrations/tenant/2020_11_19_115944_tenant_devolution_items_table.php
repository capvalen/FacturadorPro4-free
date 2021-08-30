<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDevolutionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolution_items', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('devolution_id');
            $table->unsignedInteger('item_id');
            $table->json('item');
            $table->decimal('quantity',12,4);

            $table->foreign('devolution_id')->references('id')->on('devolutions')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolution_items');
    }
}
