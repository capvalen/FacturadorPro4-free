<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIndexesToPaymentsTablesForFinances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('document_payments', function (Blueprint $table) {
            $table->index('date_of_payment');	
        });

        Schema::table('expense_payments', function (Blueprint $table) {
            $table->index('date_of_payment');	
        });
        
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->index('date_of_payment');	
        });
        
        Schema::table('sale_note_payments', function (Blueprint $table) {
            $table->index('date_of_payment');	
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('document_payments', function (Blueprint $table) {
            $table->dropIndex(['date_of_payment']);	
        });

        Schema::table('expense_payments', function (Blueprint $table) {
            $table->dropIndex(['date_of_payment']);	
        });
        
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropIndex(['date_of_payment']);	
        });

        Schema::table('sale_note_payments', function (Blueprint $table) {
            $table->dropIndex(['date_of_payment']);	
        });
        
    }
}
