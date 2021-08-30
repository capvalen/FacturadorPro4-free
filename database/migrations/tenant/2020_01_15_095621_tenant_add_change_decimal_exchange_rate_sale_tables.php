<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddChangeDecimalExchangeRateSaleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });

        Schema::table('sale_notes', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 13, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });

        Schema::table('sale_notes', function (Blueprint $table) {
            $table->decimal('exchange_rate_sale', 12, 2)->change();
        });
    }
}
