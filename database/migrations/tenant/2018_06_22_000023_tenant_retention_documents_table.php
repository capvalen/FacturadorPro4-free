<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantRetentionDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retention_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('retention_id');
            $table->string('document_type_id');
            $table->string('series');
            $table->string('number');
            $table->date('date_of_issue');
            $table->string('currency_type_id');
            $table->decimal('total_document', 10, 2);
            $table->json('payments');
            $table->json('exchange_rate');
            $table->date('date_of_retention');
            $table->decimal('total_retention', 10, 2);
            $table->decimal('total_to_pay', 10, 2);
            $table->decimal('total_payment', 10, 2);

            $table->foreign('retention_id')->references('id')->on('retentions')->onDelete('cascade');
            $table->foreign('document_type_id')->references('id')->on('cat_document_types');
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
        Schema::dropIfExists('retention_documents');
    }
}
