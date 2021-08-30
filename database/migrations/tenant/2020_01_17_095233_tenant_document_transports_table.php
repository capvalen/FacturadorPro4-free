<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDocumentTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_transports', function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->string('seat_number');
            $table->string('passenger_manifest');
            $table->string('identity_document_type_id');
            $table->string('number_identity_document');
            $table->string('passenger_fullname');
            $table->json('origin_district_id');
            $table->string('origin_address')->nullable();
            $table->json('destinatation_district_id');
            $table->string('destinatation_address')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('identity_document_type_id')->references('id')->on('cat_identity_document_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('document_transports');

    }
}
