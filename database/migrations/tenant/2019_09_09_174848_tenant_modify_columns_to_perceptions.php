<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantModifyColumnsToPerceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perceptions', function (Blueprint $table) {
            
            $table->json('establishment')->after('establishment_id');
            $table->time('time_of_issue')->after('filename');
            $table->json('customer')->after('customer_id');
            $table->dropColumn('observation');
            $table->text('observations')->nullable()->after('perception_type_id');
            $table->dropColumn('percent');
            $table->json('legends')->nullable()->after('total');
            $table->json('optional')->nullable()->after('total');
            $table->foreign('customer_id')->references('id')->on('persons');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perceptions', function (Blueprint $table) {

            $table->dropColumn('establishment');
            $table->dropColumn('time_of_issue');
            $table->dropColumn('customer');
            $table->dropColumn('observations');
            $table->text('observation');
            $table->decimal('percent', 10, 2);
            $table->dropColumn('legends');
            $table->dropColumn('optional');
            $table->dropForeign(['customer_id']);
            
        });
    }
}
