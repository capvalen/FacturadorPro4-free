<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddSocialToConfigurationEcommerce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configuration_ecommerce', function (Blueprint $table) {
            $table->text('link_facebook')->nullable()->after('script_paypal');
            $table->text('link_twitter')->nullable()->after('script_paypal');
            $table->text('link_youtube')->nullable()->after('script_paypal');
            $table->string('logo')->nullable()->after('script_paypal');

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
            $table->dropColumn(['link_facebook', 'link_twitter', 'link_youtube', 'logo']);
        });
    }
}
