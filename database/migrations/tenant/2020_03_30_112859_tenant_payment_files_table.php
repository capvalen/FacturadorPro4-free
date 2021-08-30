<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantPaymentFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_files', function (Blueprint $table) {

            $table->increments('id');
            $table->string('filename');
            $table->integer('payment_id');
            $table->string('payment_type');
            $table->index(['payment_id','payment_type'],'payment_index');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_files');
    }
}
