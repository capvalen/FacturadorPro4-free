<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPaymentTypeToPaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_method_types', function (Blueprint $table) {
            //
            $table->boolean('is_credit')->default(0)->comment('Define si es tipo credito');
            $table->boolean('is_cash')->default(0)->comment('Define si es es efectivo');
        });
        DB::table('payment_method_types')
            ->wherein('id', [
                '08',
                '05',
                '09',
            ])
            ->update(['is_credit' => 1]);
        DB::table('payment_method_types')
            ->wherein('id', [
                '10',
                '01',
            ])
            ->update(['is_cash' => 1]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_method_types', function (Blueprint $table) {
            //
            $table->dropColumn('is_credit');
            $table->dropColumn('is_cash');
        });
    }
}
