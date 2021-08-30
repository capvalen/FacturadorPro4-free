<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStatusOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_orders', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('description', 30)->nullable(false);
            $table->timestamps();
        });

        DB::table('status_orders')->insert([
          ['id' => '1', 'description' => 'Pago sin verificar', 'created_at' => now()],
          ['id' => '2', 'description' => 'Pago verificado', 'created_at' => now()],
          ['id' => '3', 'description' => 'Despachado', 'created_at' => now()],
          ['id' => '4', 'description' => 'Confirmado por el cliente', 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_orders');
    }
}
