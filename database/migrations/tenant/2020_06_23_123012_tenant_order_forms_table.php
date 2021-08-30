<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantOrderFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_forms', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->uuid('external_id');
            $table->unsignedInteger('establishment_id');
            $table->json('establishment');
            $table->char('soap_type_id', 2);
            $table->char('state_type_id', 2);
            $table->string('prefix');
            $table->date('date_of_issue')->index();
            $table->time('time_of_issue');
            $table->unsignedInteger('customer_id');
            $table->json('customer');
            $table->text('observations');
            $table->string('transport_mode_type_id');
            $table->string('transfer_reason_type_id');
            $table->string('transfer_reason_description');
            $table->date('date_of_shipping');
            $table->boolean('transshipment_indicator');
            $table->string('port_code')->nullable();
            $table->string('unit_type_id');
            $table->decimal('total_weight', 10, 2);
            $table->integer('packages_number');
            $table->integer('container_number')->nullable();
            $table->json('origin');
            $table->json('delivery');

            // $table->json('dispatcher')->nullable();
            $table->unsignedInteger('dispatcher_id');
            $table->unsignedInteger('driver_id');
            $table->json('license_plates')->nullable();

            $table->json('legends')->nullable();
            $table->json('optional')->nullable();

            $table->string('filename')->nullable();
            $table->timestamps();

            $table->foreign('dispatcher_id')->references('id')->on('dispatchers');
            $table->foreign('driver_id')->references('id')->on('drivers');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');
            $table->foreign('state_type_id')->references('id')->on('state_types');
            $table->foreign('customer_id')->references('id')->on('persons');
            $table->foreign('unit_type_id')->references('id')->on('cat_unit_types');
            $table->foreign('transport_mode_type_id')->references('id')->on('cat_transport_mode_types');
            $table->foreign('transfer_reason_type_id')->references('id')->on('cat_transfer_reason_types');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_forms');
    }
}
