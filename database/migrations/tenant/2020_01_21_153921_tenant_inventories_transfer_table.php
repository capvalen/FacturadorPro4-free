<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantInventoriesTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable();
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('warehouse_destination_id');
            $table->decimal('quantity', 12, 4);
            $table->timestamps();


            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('warehouse_destination_id')->references('id')->on('warehouses');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories_transfer');
    }
}
