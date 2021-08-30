<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantActiveTransport1004ToCatOperationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('cat_operation_types')->where('id', '1004')->update(['active' => true]);

        DB::table('cat_detraction_types')->insert([
            ['id' => '027', 'operation_type_id' => '1004', 'active' => true, 'percentage' => 4, 'description' => 'Servicio de transporte de carga'],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::table('cat_operation_types')->where('id', '1004')->update(['active' => false]);

        DB::table('cat_detraction_types')->where('id', '027')->delete();

    }
}
