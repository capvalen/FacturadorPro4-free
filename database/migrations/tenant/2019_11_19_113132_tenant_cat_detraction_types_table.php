<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantCatDetractionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('cat_detraction_types', function (Blueprint $table) {
            $table->string('id')->index();
            $table->boolean('active');
            $table->string('description');
            $table->decimal('percentage',6,2);
            $table->string('operation_type_id');
            $table->foreign('operation_type_id')->references('id')->on('cat_operation_types');
        });


        DB::table('cat_detraction_types')->insert([

            ['id' => '001', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 10, 'description' => 'Azúcar y melaza de caña'],
            ['id' => '003', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 10, 'description' => 'Alcohol etílico'],
            // ['id' => '004', 'operation_type_id' => '1002', 'active' =>false, 'percentage' => 4, 'description' => 'Recursos hidrobiológicos'],
            ['id' => '005', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 4, 'description' => 'Maíz amarillo duro'],
            ['id' => '008', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 4, 'description' => 'Madera'],
            ['id' => '016', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 10, 'description' => 'Aceite de pescado'],
            ['id' => '019', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 10, 'description' => 'Arrendamiento de bienes'],
            ['id' => '020', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 12, 'description' => 'Mantenimiento y reparación de bienes muebles'],
            ['id' => '022', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 12, 'description' => 'Otros servicios empresariales'],
            ['id' => '023', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 4, 'description' => 'Leche'],
            ['id' => '025', 'operation_type_id' => '1001', 'active' =>true, 'percentage' => 10, 'description' => 'Fabricación de bienes por encargo'],
            // ['id' => '027', 'operation_type_id' => '1004', 'active' =>false, 'percentage' => 4, 'description' => 'Servicio de transporte de carga'],
            // ['id' => '028', 'operation_type_id' => '1003', 'active' =>false, 'percentage' => 0, 'description' => 'Transporte de pasajeros'],
            
        ]);

        DB::table('cat_operation_types')->where('id', '1001')->update(['active' => true]);
 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cat_detraction_types');
    }
}
