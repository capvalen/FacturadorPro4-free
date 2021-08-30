<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantBusinessTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_turns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->string('name');
            $table->boolean('active')->default(false);
            $table->timestamps();

        });

        
        DB::table('business_turns')->insert([
            ['id' => '1', 'value' => 'hotel', 'name' => 'Hoteles', 'active' => false, 'created_at'=> now(), 'updated_at'=> now()], 
            ['id' => '2', 'value' => 'transport', 'name' => 'Empresa de transporte de pasajeros', 'active' => false, 'created_at'=> now(), 'updated_at'=> now()], 
            ['id' => '3', 'value' => 'restaurant', 'name' => 'Restaurantes', 'active' => false, 'created_at'=> now(), 'updated_at'=> now()], 
            ['id' => '4', 'value' => 'tap', 'name' => 'Grifos', 'active' => false, 'created_at'=> now(), 'updated_at'=> now()], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_turns');        
    }
}
