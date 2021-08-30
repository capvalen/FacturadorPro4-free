<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantPerceptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perception_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('perception_id');
            $table->string('document_type_id');
            $table->string('number');
            $table->date('date_of_issue');
            $table->date('date_of_perception');
            $table->string('currency_type_id');
            $table->decimal('total_document', 10, 2);
            $table->decimal('total_perception', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('exchange', 10, 2);
            $table->json('payments');

            $table->foreign('perception_id')->references('id')->on('perceptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perception_details');
    }
}
