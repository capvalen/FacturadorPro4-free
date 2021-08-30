<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantItemLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_lots', function (Blueprint $table) {

            $table->increments('id');
            $table->string('series');
            $table->date('date');
            $table->unsignedInteger('item_id'); 
            $table->timestamps();

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
        Schema::dropIfExists('item_lots');
    }
}
