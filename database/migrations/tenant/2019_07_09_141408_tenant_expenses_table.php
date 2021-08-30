<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('expense_type_id');
            $table->unsignedInteger('establishment_id'); 
            $table->unsignedInteger('supplier_id');
            $table->string('currency_type_id'); 
            $table->uuid('external_id');
            $table->integer('number');
            $table->date('date_of_issue');
            $table->time('time_of_issue');
            $table->json('supplier');
            $table->decimal('exchange_rate_sale', 12, 2);  
            $table->decimal('total', 12, 2); 
 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('supplier_id')->references('id')->on('persons');
            $table->foreign('expense_type_id')->references('id')->on('expense_types');
            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}
