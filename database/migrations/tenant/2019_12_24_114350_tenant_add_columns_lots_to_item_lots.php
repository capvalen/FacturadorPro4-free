<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsLotsToItemLots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_lots', function (Blueprint $table) {

            $table->boolean('has_sale')->default(false)->after('item_id');
            $table->unsignedInteger('item_loteable_id')->after('item_id');
            $table->string('item_loteable_type')->after('item_id');
            $table->unsignedInteger('warehouse_id')->nullable()->after('item_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_lots', function (Blueprint $table) {

            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id'); 
            $table->dropColumn('item_loteable_id'); 
            $table->dropColumn('item_loteable_type'); 
            $table->dropColumn('has_sale'); 

        });
    }
}
