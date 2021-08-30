<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->string('description');
            $table->timestamps();
        });

        DB::table('modules')->insert([

            ['value' => 'documents', 'description' => 'Ventas'],
            ['value' => 'purchases', 'description' => 'Compras'],
            ['value' => 'advanced', 'description' => 'Documentos Avanzados'],
            ['value' => 'reports', 'description' => 'Reportes'],
            ['value' => 'configuration', 'description' => 'Configuración'],

            ['value' => 'pos', 'description' => 'Punto de venta (POS)'],
            ['value' => 'dashboard', 'description' => 'Dashboard'],
            ['value' => 'inventory', 'description' => 'Inventario'],
            ['value' => 'accounting', 'description' => 'Contabilidad'],
            ['value' => 'ecommerce', 'description' => 'Ecommerce'],
            ['value' => 'cuenta', 'description' => 'Cuenta'],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
