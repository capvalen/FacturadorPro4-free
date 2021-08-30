<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSendOnlineToDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('documents', function (Blueprint $table) {
            $table->json('shipping_status')->nullable()->after('send_server');
            $table->json('sunat_shipping_status')->nullable()->after('shipping_status');
            $table->json('query_status')->nullable()->after('sunat_shipping_status');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('shipping_status');
            $table->dropColumn('sunat_shipping_status');
            $table->dropColumn('query_status');
        });
    }
}
