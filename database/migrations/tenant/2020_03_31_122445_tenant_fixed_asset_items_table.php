<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantFixedAssetItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_asset_items', function (Blueprint $table) {

            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->char('item_type_id', 2);
            $table->string('internal_id')->nullable();
            $table->string('unit_type_id');
            $table->string('currency_type_id');
            $table->decimal('purchase_unit_price', 16, 6);
            $table->string('purchase_affectation_igv_type_id');
            $table->timestamps();

            $table->foreign('item_type_id')->references('id')->on('item_types');
            $table->foreign('unit_type_id')->references('id')->on('cat_unit_types');
            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');
            $table->foreign('purchase_affectation_igv_type_id')->references('id')->on('cat_affectation_igv_types');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_items');
    }
}
