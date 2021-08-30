<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIndexesToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->index('name');	
            $table->index('second_name');	
            $table->index('description');	
            $table->index('internal_id');	
            $table->index('item_code');	
        });
        
        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');	
        });
        
        Schema::table('brands', function (Blueprint $table) {
            $table->index('name');	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['name']);	
            $table->dropIndex(['second_name']);
            $table->dropIndex(['description']);
            $table->dropIndex(['internal_id']);
            $table->dropIndex(['item_code']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['name']);	
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex(['name']);	
        });
    }
}
