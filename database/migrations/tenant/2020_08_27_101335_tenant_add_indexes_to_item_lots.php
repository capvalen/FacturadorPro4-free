<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIndexesToItemLots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_lots', function (Blueprint $table) {
            $table->index('series');	
            $table->index('date');	
            $table->index('has_sale');	
            $table->index('item_loteable_type');	
            $table->index('item_loteable_id');	
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
            $table->dropIndex(['series']);	
            $table->dropIndex(['date']);	
            $table->dropIndex(['has_sale']);	
            $table->dropIndex(['item_loteable_type']);	
            $table->dropIndex(['item_loteable_id']);	
        });
    }
}
