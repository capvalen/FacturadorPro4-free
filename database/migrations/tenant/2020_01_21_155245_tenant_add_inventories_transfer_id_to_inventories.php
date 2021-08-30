<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddInventoriesTransferIdToInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->unsignedInteger('inventories_transfer_id')->nullable()->after('lot_code');
            $table->foreign('inventories_transfer_id')->references('id')->on('inventories_transfer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn(['inventories_transfer_id']);
            $table->dropForeign(['inventories_transfer_id']);
        });
    }
}
