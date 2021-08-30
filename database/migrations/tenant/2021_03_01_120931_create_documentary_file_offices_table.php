<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentaryFileOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentary_file_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('documentary_file_id');
            $table->unsignedInteger('documentary_office_id');
            $table->unsignedInteger('documentary_action_id');
            $table->string('observation', 300)->nullable();
            $table->enum('status', ['POR DERIVAR', 'POR RECIBIR', 'EN PROCESO', 'FINALIZADO', 'ARCHIVADO'])->default('POR DERIVAR');
            $table->timestamps();

            $table->foreign('documentary_file_id')->on('documentary_files')->references('id')->onDelete('cascade');
            $table->foreign('documentary_office_id')->on('documentary_offices')->references('id')->onDelete('cascade');
            $table->foreign('documentary_action_id')->on('documentary_actions')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentary_file_offices');
    }
}
