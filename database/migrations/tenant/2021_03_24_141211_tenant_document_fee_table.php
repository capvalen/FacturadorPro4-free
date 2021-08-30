<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDocumentFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_fee', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->date('date');
            $table->string('currency_type_id');
            $table->decimal('amount', 12, 2);

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
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
        Schema::dropIfExists('document_fee');
    }
}
