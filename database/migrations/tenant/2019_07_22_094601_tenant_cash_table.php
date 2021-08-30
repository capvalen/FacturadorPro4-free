<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantCashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash', function (Blueprint $table) {
           
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->date('date_opening');
            $table->time('time_opening');
            $table->date('date_closed')->nullable();
            $table->time('time_closed')->nullable();
  
            $table->decimal('beginning_balance', 12, 4)->default(0);
            $table->decimal('final_balance', 12, 4)->default(0);
            $table->decimal('income', 12, 4)->default(0);
            $table->boolean('state')->default(false); 
 

            $table->timestamps();

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
        Schema::dropIfExists('cash');                
        //
    }
}
