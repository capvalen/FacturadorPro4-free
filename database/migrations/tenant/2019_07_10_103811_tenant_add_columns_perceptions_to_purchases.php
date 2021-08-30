<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsPerceptionsToPurchases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('total_perception', 12, 2)->nullable()->after('total'); 
            $table->integer('perception_number')->nullable()->after('total'); 
            $table->date('perception_date')->nullable()->after('total'); 
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('total_perception');
            $table->dropColumn('perception_number');
            $table->dropColumn('perception_date');
            //
        });
    }
}
