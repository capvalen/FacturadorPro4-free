<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddReferenceDataToDocumentsSaleNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('documents', function (Blueprint $table) {
            $table->string('reference_data', 500)->nullable()->after('qr');
        });

        Schema::table('sale_notes', function (Blueprint $table) {
            $table->string('reference_data', 500)->nullable()->after('plate_number');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('reference_data');
        });
        
        Schema::table('sale_notes', function (Blueprint $table) {
            $table->dropColumn('reference_data');
        });

    }
}
