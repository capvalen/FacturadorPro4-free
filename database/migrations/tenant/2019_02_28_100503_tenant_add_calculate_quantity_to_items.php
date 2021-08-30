<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddCalculateQuantityToItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('items', function (Blueprint $table) {

            $table->boolean('calculate_quantity')->default(false)->after('purchase_affectation_igv_type_id');

        });

    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('calculate_quantity'); 
            
        });

    }
}
