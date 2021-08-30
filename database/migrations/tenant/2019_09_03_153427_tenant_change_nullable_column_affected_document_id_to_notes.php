<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeNullableColumnAffectedDocumentIdToNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE notes MODIFY COLUMN affected_document_id INT(10) UNSIGNED NULL"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        DB::statement("ALTER TABLE notes MODIFY COLUMN affected_document_id INT(10) UNSIGNED NOT NULL"); 
    }

}
