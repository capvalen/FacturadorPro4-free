<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeColumnTypeToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        DB::statement("ALTER TABLE users CHANGE type type ENUM('admin', 'seller','integrator') NOT NULL"); 
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        DB::statement("ALTER TABLE users CHANGE type type ENUM('admin', 'seller') NOT NULL"); 
        
    }
}
