<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifyDecimalsToItemUnitTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_unit_types', function (Blueprint $table) {
            
            $table->decimal('price1', 12, 4)->change();
            $table->decimal('price2', 12, 4)->change();
            $table->decimal('price3', 12, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_unit_types', function (Blueprint $table) {
            
            $table->decimal('price1', 12, 2)->change();
            $table->decimal('price2', 12, 2)->change();
            $table->decimal('price3', 12, 2)->change();
        });
    }
}
