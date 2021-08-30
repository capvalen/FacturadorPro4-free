<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantTechnicalServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_services', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->char('soap_type_id', 2);
            $table->unsignedInteger('customer_id');
            $table->json('customer');
            $table->string('cellphone');
            $table->date('date_of_issue')->index();
            $table->time('time_of_issue');
            $table->text('description');
            $table->text('state');
            $table->text('reason');
            $table->string('serial_number')->index();
            $table->string('filename')->nullable();

            $table->decimal('cost', 12, 2)->default(0); 
            $table->decimal('prepayment', 12, 2)->default(0); 
            $table->text('activities')->nullable();
 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('customer_id')->references('id')->on('persons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technical_services');
    }
}
