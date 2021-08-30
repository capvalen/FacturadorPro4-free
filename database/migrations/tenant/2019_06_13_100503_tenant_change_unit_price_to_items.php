<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeUnitPriceToItems extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('sale_unit_price', 12, 4)->change();
            $table->decimal('purchase_unit_price', 12, 4)->change();
        });

        Schema::table('document_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 4)->change();
            $table->decimal('unit_value', 12, 4)->change();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('sale_unit_price', 12, 2)->change();
            $table->decimal('purchase_unit_price', 12, 2)->change();
        });

        Schema::table('document_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->change();
            $table->decimal('unit_value', 12, 2)->change();
        });
    }
}
