<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantAddColumnsPurchasesToPaymentMethodTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_method_types', function (Blueprint $table) {
            $table->integer('number_days')->nullable()->after('has_card'); 
            $table->decimal('charge',12,2)->nullable()->after('has_card'); 
        });

        DB::table('payment_method_types')->insert([
            ['id' => '05', 'description' => 'Factura a 30 días', 'has_card' => false, 'number_days' => 30, 'charge' => null],
            ['id' => '06', 'description' => 'Tarjeta crédito visa', 'has_card' => true, 'number_days' => null, 'charge' => 3.68],
            ['id' => '07', 'description' => 'Contado contraentrega', 'has_card' => false, 'number_days' => null, 'charge' => null], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_method_types', function (Blueprint $table) {
            $table->dropColumn('number_days');
            $table->dropColumn('charge');
        });
    }
}
