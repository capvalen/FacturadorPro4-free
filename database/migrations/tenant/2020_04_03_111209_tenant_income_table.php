<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('income_type_id');
            $table->unsignedInteger('income_reason_id');
            $table->char('soap_type_id', 2);
            $table->char('state_type_id', 2);
            $table->unsignedInteger('establishment_id'); 
            $table->string('customer'); 
            $table->string('currency_type_id'); 
            $table->uuid('external_id');
            $table->integer('number')->index();
            $table->date('date_of_issue')->index();
            $table->time('time_of_issue');
            $table->decimal('exchange_rate_sale', 13, 3);  
            $table->decimal('total', 12, 2); 
 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('income_type_id')->references('id')->on('income_types');
            $table->foreign('income_reason_id')->references('id')->on('income_reasons');
            $table->foreign('state_type_id')->references('id')->on('state_types');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
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
        Schema::dropIfExists('income');
    }
}
