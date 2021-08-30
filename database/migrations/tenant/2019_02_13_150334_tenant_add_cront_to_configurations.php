<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddCrontToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('cron')->default(true)->after('send_auto');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('cron');
        });
    }
}
