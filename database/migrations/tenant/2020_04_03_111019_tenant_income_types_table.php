<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantIncomeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('income_types', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('description'); 
            $table->timestamps(); 
        });

        DB::table('income_types')->insert([
            ['id' => '1', 'description' => 'INGRESOS FINANCIEROS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '2', 'description' => 'PRESTAMOS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '3', 'description' => 'OTROS', 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('income_types');
    }
}
