<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddItemsToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('tenant')->table('modules')->insert([
            ['id' => 17, 'description' => 'Productos/Servicios', 'value' => 'items', 'order_menu' => 16],
            ['id' => 18, 'description' => 'Clientes', 'value' => 'persons', 'order_menu' => 17],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('tenant')->table('modules')->whereIn('id', [17, 18]);
    }
}
