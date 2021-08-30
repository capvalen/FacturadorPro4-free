<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->char('item_type_id', 2);
            $table->string('internal_id', 30)->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_code_gs1')->nullable();

            $table->string('unit_type_id');
            $table->string('currency_type_id');
            $table->decimal('sale_unit_price', 12, 2);
            $table->decimal('purchase_unit_price', 12, 2)->default(0);

            $table->boolean('has_isc')->default(false);
            $table->string('system_isc_type_id')->nullable();
            $table->decimal('percentage_isc', 12, 2)->default(0);
            $table->decimal('suggested_price', 12, 2)->default(0);

            $table->string('sale_affectation_igv_type_id');
            $table->string('purchase_affectation_igv_type_id');

            $table->decimal('stock', 12, 2)->default(0);
            $table->decimal('stock_min', 12, 2)->default(0);

            $table->json('attributes')->nullable();
            $table->timestamps();

            $table->foreign('item_type_id')->references('id')->on('item_types');
            $table->foreign('unit_type_id')->references('id')->on('cat_unit_types');
            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');
            $table->foreign('system_isc_type_id')->references('id')->on('cat_system_isc_types');
            $table->foreign('sale_affectation_igv_type_id')->references('id')->on('cat_affectation_igv_types');
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
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_types');
    }
}