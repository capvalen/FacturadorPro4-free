<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantExpenseReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
        });

        
        DB::table('expense_reasons')->insert([
            ['id' => '1', 'description' => 'Varios'],
            ['id' => '2', 'description' => 'Representación de la organización'],
            ['id' => '3', 'description' => 'Trabajo de campo'], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_reasons');        
    }
}
