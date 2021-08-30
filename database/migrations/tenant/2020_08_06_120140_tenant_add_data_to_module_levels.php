<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDataToModuleLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('module_levels')->insert([
            ['value' => 'sale-opportunity', 'description' => 'Oportunidad de venta', 'module_id' => 1],
            ['value' => 'contracts', 'description' => 'Contratos', 'module_id' => 1],
            ['value' => 'order-note', 'description' => 'Pedidos', 'module_id' => 1],
            ['value' => 'technical-service', 'description' => 'Servicios de soporte técnico', 'module_id' => 1],
        ]);

        DB::table('module_levels')->where('value', 'incentives')->update(['description' => 'Comisiones']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::table('module_levels')->whereIn('value', ['sale-opportunity', 'contracts', 'order-note', 'technical-service'])->delete();

        DB::table('module_levels')->where('value', 'incentives')->update(['description' => 'Incentivos']);

    }
}
