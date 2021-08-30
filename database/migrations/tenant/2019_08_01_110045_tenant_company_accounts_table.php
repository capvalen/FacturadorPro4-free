<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantCompanyAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_accounts', function (Blueprint $table) {

            $table->increments('id');
            $table->string('subtotal_pen'); 
            $table->string('total_pen'); 
            $table->string('igv_pen');             
            $table->string('subtotal_usd'); 
            $table->string('total_usd'); 
            $table->string('igv_usd'); 

        });

        DB::table('company_accounts')->insert([ 
            [
                'subtotal_pen'=> '70111','total_pen' => '12121', 'igv_pen' => '40111',
                'subtotal_usd'=> '70111','total_usd' => '12122', 'igv_usd' => '40111',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_accounts');
    }
}
