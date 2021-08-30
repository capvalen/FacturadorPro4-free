<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeElectronicoAtCatDocumentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('cat_document_types')->where('id', '09')->update(['description' => 'GUIA DE REMISIÓN REMITENTE']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cat_document_types')->where('id', '09')->update(['description' => 'GUIA DE REMISIÓN REMITENTE ELECTRÓNICA']);
    }
}
