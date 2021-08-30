<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeDecimalsUnitPriceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('unit_price', 16, 6)->change();
            $table->decimal('unit_value', 16, 6)->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 16, 6)->change();
            $table->decimal('unit_value', 16, 6)->change();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->decimal('unit_price', 16, 6)->change();
            $table->decimal('unit_value', 16, 6)->change();
        });

        Schema::table('sale_note_items', function (Blueprint $table) {
            $table->decimal('unit_price', 16, 6)->change();
            $table->decimal('unit_value', 16, 6)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_value', 12, 2)->change();
        });

        Schema::table('purchase_order_items', function (Blueprint $table) { 
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_value', 12, 2)->change();
        });

        Schema::table('quotation_items', function (Blueprint $table) { 
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_value', 12, 2)->change();
        });

        Schema::table('sale_note_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_value', 12, 2)->change();
        });
    }
}
