<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeStateTypeIdToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['state_type_id']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('state_type_id')->references('id')->on('contract_state_types');  
        });

    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['state_type_id']);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('state_type_id')->references('id')->on('state_types');  
        });

    }
}
