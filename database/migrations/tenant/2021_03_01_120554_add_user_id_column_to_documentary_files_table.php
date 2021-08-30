<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdColumnToDocumentaryFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documentary_files', function (Blueprint $table) {
            $table->enum('status', ['RECIBIDO', 'DERIVADO', 'FINALIZADO', 'ARCHIVADO'])->default('RECIBIDO')->after('observation');
            $table->unsignedInteger('user_id')->nullable()->after('id');

            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentary_files', function (Blueprint $table) {
            $table->dropForeign('documentary_files_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('status');
        });
    }
}
