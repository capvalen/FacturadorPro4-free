<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSoapTypeIdToExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->char('soap_type_id', 2)->nullable()->after('user_id');
            $table->foreign('soap_type_id')->references('id')->on('soap_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->dropForeign(['soap_type_id']);
            $table->dropColumn('soap_type_id');

        });
    }
}
