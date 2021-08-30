<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderItemsToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('tenant')->table('modules')->where('id', 17)->update(['order_menu' => 5]);
        DB::connection('tenant')->table('modules')->where('id', 2)->update(['order_menu' => 7]);
        DB::connection('tenant')->table('modules')->where('id', 18)->update(['order_menu' => 6]);
        DB::connection('tenant')->table('modules')->where('id', 8)->update(['order_menu' => 7]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
