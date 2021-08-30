<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDataToDocumentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cat_document_types')->insert([ 
            ['id' => 'GU75', 'active' => true,  'short' => null, 'description' => 'GUÍA'],
            ['id' => 'NE76', 'active' => true,  'short' => null, 'description' => 'NOTA DE ENTRADA'], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cat_document_types')->where('id', 'GU75')->delete();
        DB::table('cat_document_types')->where('id', 'NE76')->delete();
    }
}
