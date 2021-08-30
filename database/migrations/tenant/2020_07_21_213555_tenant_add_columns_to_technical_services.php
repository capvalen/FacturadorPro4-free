<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddColumnsToTechnicalServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technical_services', function (Blueprint $table) {

            $table->string('brand')->nullable();
            $table->string('equipment')->nullable();
            $table->text('important_note')->nullable();

            $table->boolean('repair')->default(false);
            $table->boolean('warranty')->default(false);
            $table->boolean('maintenance')->default(false);
            $table->boolean('diagnosis')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('technical_services', function (Blueprint $table) {
            $table->dropColumn(['brand','equipment','important_note', 'repair', 'warranty', 'maintenance', 'diagnosis']);
        });
    }
}
