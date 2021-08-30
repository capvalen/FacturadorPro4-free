<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeOrderItemToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('system')->table('modules')->where('id', 17)->update(['sort' => 5]);
        DB::connection('system')->table('modules')->where('id', 2)->update(['sort' => 7]);
        DB::connection('system')->table('modules')->where('id', 18)->update(['sort' => 6]);
        DB::connection('system')->table('modules')->where('id', 8)->update(['sort' => 7]);
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
