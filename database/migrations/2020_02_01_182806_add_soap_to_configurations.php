<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoapToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {

            $table->char('soap_send_id', 2)->nullable()->default('01')->after('certificate');
            $table->char('soap_type_id', 2)->nullable()->default('01');
            $table->string('soap_username')->nullable();
            $table->string('soap_password')->nullable();
            $table->string('soap_url')->nullable();

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
            //
        });
    }
}
