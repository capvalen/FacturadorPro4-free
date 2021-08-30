<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDropCompactSidebarToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {

            $table->dropColumn('compact_sidebar');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {

            $table->boolean('compact_sidebar')->default(true)->after('locked_tenant');

        });
    }
}
