<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddTagToConfigurationEcommerce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->text('tag_shipping')->nullable()->after('link_facebook');
            $table->text('tag_dollar')->nullable()->after('link_facebook');
            $table->text('tag_support')->nullable()->after('link_facebook');
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
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->dropColumn(['tag_shipping', 'tag_dollar', 'tag_support']);
        });
    }
}
