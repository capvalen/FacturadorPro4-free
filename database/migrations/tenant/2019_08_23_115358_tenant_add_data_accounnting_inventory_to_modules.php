<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantAddDataAccounntingInventoryToModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('modules')->insert([ 
            ['id'=> '8','value' => 'inventory', 'description' => 'Inventario'],
            ['id'=> '9','value' => 'accounting', 'description' => 'Contabilidad'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::table('modules')->where('id', 8)->delete();
        DB::table('modules')->where('id', 9)->delete();

    }
}
