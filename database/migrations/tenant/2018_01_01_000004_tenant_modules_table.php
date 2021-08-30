<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModulesTable extends Migration
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
            ['value' => 'configuration', 'description' => 'Configuration'],
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