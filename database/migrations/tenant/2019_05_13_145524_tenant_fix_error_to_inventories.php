<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantFixErrorToInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if ((Schema::hasColumn('inventories', 'date_of_issue')) && (Schema::hasColumn('inventories', 'inventory_kardexable_id')) && (Schema::hasColumn('inventories', 'inventory_kardexable_type'))) {
            Schema::table('inventories', function(Blueprint $table) {
                $table->dropColumn(['date_of_issue', 'inventory_kardexable_id', 'inventory_kardexable_type']);
            });
        }
        
        if (!Schema::hasColumn('inventories', 'type')) {
            Schema::table('inventories', function(Blueprint $table) {
                $table->enum('type', [1, 2, 3])->after('id');
            });
        }
        
        if (!Schema::hasColumn('inventories', 'description')) {
            Schema::table('inventories', function(Blueprint $table) {
                $table->string('description')->after('type');
            });
        }
        
        if (!Schema::hasColumn('inventories', 'warehouse_destination_id')) {
            Schema::table('inventories', function(Blueprint $table) {
                $table->unsignedInteger('warehouse_destination_id')->nullable()->after('warehouse_id');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('inventories', function(Blueprint $table) {});
    }
}
