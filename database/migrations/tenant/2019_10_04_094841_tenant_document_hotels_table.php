<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDocumentHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_hotels', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedInteger('document_id');

            $table->string('number');
            $table->string('name');
            $table->string('identity_document_type_id');

            $table->enum('sex', ['M', 'F']);
            $table->integer('age');
            $table->enum('civil_status', ['S', 'C', 'V', 'D']);
            $table->string('nacionality');
            $table->string('origin');
            $table->integer('room_number');

            $table->date('date_entry');
            $table->time('time_entry');
            $table->date('date_exit');
            $table->time('time_exit');
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
        Schema::dropIfExists('document_hotels');
    }
}
