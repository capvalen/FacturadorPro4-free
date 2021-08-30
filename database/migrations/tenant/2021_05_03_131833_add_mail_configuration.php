<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->text('smtp_host')
                ->nullable()
                ->after('finances')
                ->comment('Host de correo del cliente');
            $table->unsignedInteger('smtp_port')
                ->default(0)
                ->after('finances')
                ->comment('Puerto de correo del cliente');
            $table->text('smtp_user')
                ->nullable()
                ->after('finances')
                ->comment('Nombre de usuario para el envio de correo');
            $table->text('smtp_password')
                ->nullable()
                ->after('finances')
                ->comment('contraseña de usuario para el envio de correo');
            $table->text('smtp_encryption')
                ->nullable()
                ->after('finances')
                ->comment('Tipo de cifrado de correo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('smtp_host');
            $table->dropColumn('smtp_port');
            $table->dropColumn('smtp_user');
            $table->dropColumn('smtp_password');
            $table->dropColumn('smtp_encryption');

        });
    }
}
