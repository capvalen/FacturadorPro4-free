<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDocumentPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('card_brands', function (Blueprint $table) {
            $table->char('id', 2)->index();
            $table->string('description');
        });

        DB::table('card_brands')->insert([
            ['id' => '01', 'description' => 'Visa'],
            ['id' => '02', 'description' => 'Mastercard'],
        ]);

        Schema::create('payment_method_types', function (Blueprint $table) {
            $table->char('id', 2)->index();
            $table->string('description');
            $table->boolean('has_card')->default(false);
        });

        DB::table('payment_method_types')->insert([
            ['id' => '01', 'description' => 'Efectivo'          , 'has_card' => false],
            ['id' => '02', 'description' => 'Tarjeta de crédito', 'has_card' => true],
            ['id' => '03', 'description' => 'Tarjeta de débito',  'has_card' => true],
            ['id' => '04', 'description' => 'Transferencia',      'has_card' => false],
        ]);

        Schema::create('document_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->date('date_of_payment');
            $table->char('payment_method_type_id', 2);
            $table->boolean('has_card')->default(false);
            $table->char('card_brand_id', 2)->nullable();
            $table->string('reference')->nullable();
            $table->decimal('payment', 12, 2);

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('card_brand_id')->references('id')->on('card_brands');
            $table->foreign('payment_method_type_id')->references('id')->on('payment_method_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_payments');
        Schema::dropIfExists('payment_method_types');
        Schema::dropIfExists('card_brands');
    }
}
