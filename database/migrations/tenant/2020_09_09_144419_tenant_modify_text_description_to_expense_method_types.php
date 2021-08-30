<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifyTextDescriptionToExpenseMethodTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('expense_method_types')->where('id', '1')->update(['description' => 'CAJA GENERAL']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('expense_method_types')->where('id', '1')->update(['description' => 'Caja chica']);
    }
}
