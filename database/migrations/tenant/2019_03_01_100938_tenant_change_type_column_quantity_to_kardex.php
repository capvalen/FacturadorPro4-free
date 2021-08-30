<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeTypeColumnQuantityToKardex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 

        DB::statement('ALTER TABLE kardex MODIFY quantity DECIMAL(12,4) NOT NULL'); 

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 

        DB::statement('ALTER TABLE kardex MODIFY quantity INT(11) NOT NULL');

    }
}
