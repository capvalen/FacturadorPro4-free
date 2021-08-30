<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddUserIdToGlobalPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_payments', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->nullable()->after('payment_type');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_payments', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');  
            
        });
    }
}
