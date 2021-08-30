<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsAditionalToModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('modules', function (Blueprint $table) {
            $table->integer('order_menu')->after('description');
        });
        DB::table('modules')->where('id', 7)->update(["order_menu" =>1]);
        DB::table('modules')->where('id', 1)->update(["order_menu" =>2]);
        DB::table('modules')->where('id', 6)->update(["order_menu" =>3]);
        DB::table('modules')->where('id', 10)->update(["order_menu" =>4]);
        DB::table('modules')->where('id', 2)->update(["order_menu" =>5]);
        DB::table('modules')->where('id', 8)->update(["order_menu" =>6]);
        DB::table('modules')->where('id', 14)->update(["order_menu" =>7]);
        DB::table('modules')->where('id', 3)->update(["order_menu" =>8]);
        DB::table('modules')->where('id', 4)->update(["order_menu" =>9]);
        DB::table('modules')->where('id', 9)->update(["order_menu" =>10]);
        DB::table('modules')->where('id', 12)->update(["order_menu" =>11]);
        DB::table('modules')->where('id', 5)->update(["order_menu" =>12]);
        DB::table('modules')->where('id', 11)->update(["order_menu" =>13]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('order_menu');
            //
        });
    }
}
