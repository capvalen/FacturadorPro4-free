<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddCotizactionFinanceConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('configurations', function (Blueprint $table) {
          $table->boolean('cotizaction_finance')->default(true)->after('terms_condition');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('configurations', function (Blueprint $table) {
          $table->dropColumn('cotizaction_finance');
      });
    }
}
