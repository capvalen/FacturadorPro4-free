<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPurchaseExpenseToCashDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_documents', function (Blueprint $table) {

            $table->unsignedInteger('purchase_id')->nullable()->after('sale_note_id');
            $table->unsignedInteger('expense_id')->nullable()->after('sale_note_id');

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_documents', function (Blueprint $table) {

            $table->dropForeign(['purchase_id']);
            $table->dropForeign(['expense_id']);

            $table->dropColumn('purchase_id');  
            $table->dropColumn('expense_id');  
            
        });
    }
}
