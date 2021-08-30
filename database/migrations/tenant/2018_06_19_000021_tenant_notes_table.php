<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->enum('note_type', ['credit', 'debit']);
            $table->string('note_credit_type_id')->nullable();
            $table->string('note_debit_type_id')->nullable();
            $table->string('note_description');
            $table->unsignedInteger('affected_document_id');

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('note_credit_type_id')->references('id')->on('cat_note_credit_types');
            $table->foreign('note_debit_type_id')->references('id')->on('cat_note_debit_types');
            $table->foreign('affected_document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
