<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddLockedToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
 
        Schema::table('users', function (Blueprint $table) {

            $table->boolean('locked')->default(false)->after('establishment_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locked'); 
            
        });

    }
}
