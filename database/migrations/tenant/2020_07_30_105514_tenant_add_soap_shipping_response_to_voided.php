<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSoapShippingResponseToVoided extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voided', function (Blueprint $table) {
            $table->json('soap_shipping_response')->nullable()->after('has_cdr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voided', function (Blueprint $table) {
            $table->dropColumn('soap_shipping_response');
        });
    }
}
