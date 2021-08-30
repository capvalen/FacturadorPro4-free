<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeColumnsToExchangeRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('buy', 'purchase')->after('date');
            $table->decimal('purchase_original', 13, 3)->after('date');
            $table->renameColumn('sell', 'sale')->after('date');
            $table->decimal('sale_original', 13, 3)->after('date');
//            $table->dropColumn('date_original');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchange_rates', function (Blueprint $table) {
            $table->renameColumn('purchase', 'buy');
            $table->renameColumn('sale', 'sell');
            $table->dropColumn('purchase_original');
            $table->dropColumn('sale_original');
        });
    }
}
