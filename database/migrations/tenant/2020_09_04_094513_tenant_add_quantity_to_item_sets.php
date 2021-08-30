<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddQuantityToItemSets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_sets', function (Blueprint $table) {
            $table->decimal('quantity', 12, 4)->default(1)->after('individual_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_sets', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
