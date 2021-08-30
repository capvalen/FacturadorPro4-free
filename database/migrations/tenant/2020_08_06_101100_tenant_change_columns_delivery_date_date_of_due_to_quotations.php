<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeColumnsDeliveryDateDateOfDueToQuotations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('date_of_due')->nullable()->change();
            $table->string('delivery_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement("ALTER TABLE quotations CHANGE date_of_due date_of_due DATE NULL"); 
        DB::statement("ALTER TABLE quotations CHANGE delivery_date delivery_date DATE NULL"); 

    }
}
