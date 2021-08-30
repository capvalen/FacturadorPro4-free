<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantPaymentConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_conditions', function (Blueprint $table) {
            $table->string('id')->index();
            $table->string('name');
            $table->integer('days')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_active')->default(true);
        });

        DB::table('payment_conditions')->insert([
            ['id' => '01', 'name' => 'Contado',  'days' => 0, 'is_active' => true, 'is_locked' => true],
            ['id' => '02', 'name' => 'Crédito',  'days' => 0, 'is_active' => true, 'is_locked' => true],
//            ['id' => 5, 'name' => 'Factura a 7 días', 'days' => 7, 'is_active' => true, 'is_locked' => false],
//            ['id' => 3, 'name' => 'Factura a 15 días', 'days' => 15, 'is_active' => true, 'is_locked' => false],
//            ['id' => 4, 'name' => 'Factura a 30 días', 'days' => 30, 'is_active' => true, 'is_locked' => false],
//            ['id' => 6, 'name' => 'Factura a 45 días', 'days' => 45, 'is_active' => true, 'is_locked' => false],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_conditions');
    }
}
