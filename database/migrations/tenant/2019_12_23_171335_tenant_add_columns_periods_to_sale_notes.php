<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsPeriodsToSaleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->string('type_period')->index()->nullable()->after('apply_concurrency');
            $table->integer('quantity_period')->index()->nullable()->after('apply_concurrency');
            $table->date('automatic_date_of_issue')->index()->nullable()->after('apply_concurrency');
            $table->boolean('enabled_concurrency')->index()->default(false)->after('apply_concurrency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn('type_period');  
            $table->dropColumn('quantity_period');  
            $table->dropColumn('automatic_date_of_issue');  
            $table->dropColumn('enabled_concurrency');  
        });
    }
}
