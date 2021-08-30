<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeExpenseReasonIdToExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->dropForeign(['expense_reason_id']);
            $table->foreign('expense_reason_id')->references('id')->on('expense_reasons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->dropForeign(['expense_reason_id']);
            $table->foreign('expense_reason_id')->references('id')->on('expenses');
        });
    }
}
