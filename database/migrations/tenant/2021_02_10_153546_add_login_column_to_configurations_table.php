<?php

use App\Models\Tenant\Configuration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginColumnToConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('login')->nullable()->after('default_document_type_03');
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
				'facebook'          => null,
				'twitter'           => null,
				'instagram'         => null,
				'linkedin'          => null,
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
        });
    }
}
