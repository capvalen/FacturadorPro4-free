<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeTextDescriptionToCatDocumentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('cat_document_types')->where('id', '09')->update(['description' => 'GUIA DE REMISIÓN REMITENTE ELECTRÓNICA']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::table('cat_document_types')->where('id', '09')->update(['description' => 'GUIA DE REMISIÓN REMITENTE']);
        
    }
}
