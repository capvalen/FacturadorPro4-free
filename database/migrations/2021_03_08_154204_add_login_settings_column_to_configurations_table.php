<?php

use App\Models\System\Configuration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginSettingsColumnToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('use_login_global')->default(false);
            $table->text('login')->nullable();
        });

        $configuration = Configuration::first();
        if ($configuration) {
			$login = [
				'type'              => 'image',
				'image'             => asset('images/login-v2.svg'),
				'position_form'     => 'right',
				'show_logo_in_form' => false,
				'position_logo'     => 'top-left',
				'show_socials'      => false,
			];
			$configuration->login = $login;
			$configuration->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('login');
            $table->dropColumn('use_login_global');
        });
    }
}
