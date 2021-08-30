<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddQuotationIdToDocuments extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('quotation_id')->nullable()->after('purchase_order');
            $table->foreign('quotation_id')->references('id')->on('quotations');
        });
    }
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropColumn('quotation_id');   
        });
    }
}
