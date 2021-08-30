<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPersonTypesToPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->string('comment')->nullable()->after('perception_agent'); 
            $table->unsignedInteger('person_type_id')->nullable()->after('perception_agent');
            $table->foreign('person_type_id')->references('id')->on('person_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('comment');
            $table->dropForeign(['person_type_id']);
            $table->dropColumn('person_type_id');  
        });
    }
}
