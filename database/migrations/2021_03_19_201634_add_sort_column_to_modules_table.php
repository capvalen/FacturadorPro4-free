<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortColumnToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->integer('sort')->default(1)->after('description');
        });

        DB::connection('system')->table('modules')->insert([
            ['id' => 17, 'description' => 'Productos/Servicios', 'value' => 'items', 'sort' => 16],
            ['id' => 18, 'description' => 'Clientes', 'value' => 'persons', 'sort' => 17],
        ]);

        DB::connection('system')->table('modules')->where('id', 1)->update(['sort' => 2]);
        DB::connection('system')->table('modules')->where('id', 2)->update(['sort' => 5]);
        DB::connection('system')->table('modules')->where('id', 3)->update(['sort' => 8]);
        DB::connection('system')->table('modules')->where('id', 4)->update(['sort' => 9]);
        DB::connection('system')->table('modules')->where('id', 5)->update(['sort' => 12]);
        DB::connection('system')->table('modules')->where('id', 6)->update(['sort' => 3]);
        DB::connection('system')->table('modules')->where('id', 7)->update(['sort' => 1]);
        DB::connection('system')->table('modules')->where('id', 8)->update(['sort' => 6]);
        DB::connection('system')->table('modules')->where('id', 9)->update(['sort' => 10]);
        DB::connection('system')->table('modules')->where('id', 10)->update(['sort' => 4]);
        DB::connection('system')->table('modules')->where('id', 11)->update(['sort' => 13]);
        DB::connection('system')->table('modules')->where('id', 12)->update(['sort' => 11]);
        DB::connection('system')->table('modules')->where('id', 14)->update(['sort' => 7]);
        DB::connection('system')->table('modules')->where('id', 15)->update(['sort' => 14]);
        DB::connection('system')->table('modules')->where('id', 16)->update(['sort' => 15]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
