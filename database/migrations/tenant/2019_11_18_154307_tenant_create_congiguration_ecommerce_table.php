<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantCreateCongigurationEcommerceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuration_ecommerce', function (Blueprint $table) {
            $table->increments('id');
            $table->string('information_contact_name');
            $table->string('information_contact_email');
            $table->string('information_contact_phone');
            $table->text('script_paypal')->nullable();

            $table->text('token_public_culqui')->nullable();
            $table->text('token_private_culqui')->nullable();


            $table->timestamps();
        });

        DB::connection('tenant')->table('configuration_ecommerce')->insert([
            ['information_contact_name' => 'Admin', 'information_contact_email' => 'admin@mail.com', 'information_contact_phone' => '01 505-5555'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_ecommerce');
    }
}
