<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantCashTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_transactions', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('cash_id');
            $table->char('payment_method_type_id', 2);
            $table->date('date');
            $table->string('description')->nullable();
            $table->decimal('payment', 14, 4);
            $table->foreign('cash_id')->references('id')->on('cash')->onDelete('cascade'); 
            $table->foreign('payment_method_type_id')->references('id')->on('payment_method_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_transactions');
    }

}
