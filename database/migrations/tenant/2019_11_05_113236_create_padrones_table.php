<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePadronesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('padrones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ruc')->index();
            $table->string('nombre_razon_social', 155);
            $table->string('estado_contribuyente', 100);
            $table->string('condicion_domicilio', 100);
            $table->string('ubigeo', 50);
            $table->string('tipo_via', 20);
            $table->string('nombre_via', 50);
            $table->string('codigo_zona', 155);
            $table->string('tipo_zona', 20);
            $table->string('numero', 155);
            $table->string('interior', 50);
            $table->string('lote', 20);
            $table->string('departamento', 100);
            $table->string('manzana', 20);
            $table->string('kilometro', 20);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('padrones');
    }
}
