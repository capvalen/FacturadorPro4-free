<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifyColumnsToPerceptionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('perception_details', 'perception_documents');

        Schema::table('perception_documents', function (Blueprint $table) {
            
            $table->string('series');            
            $table->dropColumn('total');
            $table->dropColumn('exchange');
            $table->json('exchange_rate');
            $table->decimal('total_to_pay', 10, 2);
            $table->decimal('total_payment', 10, 2);
            $table->foreign('document_type_id')->references('id')->on('cat_document_types');
            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('perception_documents', function (Blueprint $table) {
            
            $table->dropColumn('series');    
            $table->decimal('total', 10, 2);
            $table->decimal('exchange', 10, 2);
            $table->dropColumn('exchange_rate');
            $table->dropColumn('total_to_pay');
            $table->dropColumn('total_payment');
            $table->dropForeign(['document_type_id']);
            $table->dropForeign(['currency_type_id']);

        });

        Schema::rename('perception_documents', 'perception_details');

    }
}
