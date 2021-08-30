<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TenantInventoryTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->string('id')->index(); 
            $table->string('name'); 
            $table->enum('type', ['input', 'output']);

        });

        DB::table('inventory_transactions')->insert([
            ['id' => '02', 'name' => 'Compra nacional', 'type' => 'input'],
            ['id' => '03', 'name' => 'Consignación recibida', 'type' => 'input'],
            ['id' => '05', 'name' => 'Devolución recibida', 'type' => 'input'], 
            ['id' => '16', 'name' => 'Inventario inicial', 'type' => 'input'],
            ['id' => '18', 'name' => 'Entrada de importación', 'type' => 'input'],
            ['id' => '19', 'name' => 'Ingreso de producción', 'type' => 'input'], 
            ['id' => '20', 'name' => 'Entrada por devolución de producción', 'type' => 'input'],
            ['id' => '21', 'name' => 'Entrada por transferencia entre almacenes', 'type' => 'input'],
            ['id' => '22', 'name' => 'Entrada por identificación erronea', 'type' => 'input'], 
            ['id' => '24', 'name' => 'Entrada por devolución del cliente', 'type' => 'input'],
            ['id' => '26', 'name' => 'Entrada para servicio de producción', 'type' => 'input'],
            ['id' => '29', 'name' => 'Entrada de bienes en prestamo', 'type' => 'input'], 
            ['id' => '31', 'name' => 'Entrada de bienes en custodia', 'type' => 'input'],
            ['id' => '50', 'name' => 'Ingreso temporal', 'type' => 'input'],
            ['id' => '52', 'name' => 'Ingreso por transformación', 'type' => 'input'], 
            ['id' => '54', 'name' => 'Ingreso de producción', 'type' => 'input'],
            ['id' => '55', 'name' => 'Entrada de importación', 'type' => 'input'],
            ['id' => '57', 'name' => 'Entrada por conversión de medida', 'type' => 'input'], 
            ['id' => '91', 'name' => 'Ingreso por transformación', 'type' => 'input'],
            ['id' => '93', 'name' => 'Ingreso temporal', 'type' => 'input'],
            ['id' => '96', 'name' => 'Entrada por conversión de medida', 'type' => 'input'], 
            ['id' => '99', 'name' => 'Otros', 'type' => 'input'], 


            ['id' => '01', 'name' => 'Venta nacional', 'type' => 'output'], 
            ['id' => '04', 'name' => 'Consignación entregada', 'type' => 'output'], 
            ['id' => '06', 'name' => 'Devolución entregada', 'type' => 'output'], 
            ['id' => '07', 'name' => 'Bonificación', 'type' => 'output'], 
            ['id' => '08', 'name' => 'Premio', 'type' => 'output'], 
            ['id' => '09', 'name' => 'Donación', 'type' => 'output'], 
            ['id' => '10', 'name' => 'Salida a producción', 'type' => 'output'], 
            ['id' => '11', 'name' => 'Salida por transferencia entre almacenes', 'type' => 'output'], 
            ['id' => '12', 'name' => 'Retiro', 'type' => 'output'], 
            ['id' => '13', 'name' => 'Mermas', 'type' => 'output'], 
            ['id' => '14', 'name' => 'Desmedros', 'type' => 'output'], 
            ['id' => '15', 'name' => 'Destrucción', 'type' => 'output'], 
            ['id' => '17', 'name' => 'Exportación', 'type' => 'output'], 
            ['id' => '23', 'name' => 'Salida por identificación erronea', 'type' => 'output'], 
            ['id' => '25', 'name' => 'Salida por devolución al proveedor', 'type' => 'output'], 
            ['id' => '27', 'name' => 'Salida por servicio de producción', 'type' => 'output'], 
            ['id' => '28', 'name' => 'Ajuste por diferencia de inventario', 'type' => 'output'], 
            ['id' => '30', 'name' => 'Salida de bienes en prestamo', 'type' => 'output'], 
            ['id' => '32', 'name' => 'Salida de bienes en custodia', 'type' => 'output'], 
            ['id' => '33', 'name' => 'Muestras médicas', 'type' => 'output'], 
            ['id' => '34', 'name' => 'Publicidad', 'type' => 'output'], 
            ['id' => '35', 'name' => 'Gastos de representación', 'type' => 'output'], 
            ['id' => '36', 'name' => 'Retiro para entrega a trabajadores', 'type' => 'output'], 
            ['id' => '37', 'name' => 'Retiro por convenio colectivo', 'type' => 'output'], 
            ['id' => '38', 'name' => 'Retiro por sustitución de bien siniestrado', 'type' => 'output'], 
            ['id' => '51', 'name' => 'Salida temporal', 'type' => 'output'], 
            ['id' => '53', 'name' => 'Salida para servicios terceros', 'type' => 'output'], 
            ['id' => '56', 'name' => 'Salida por conversión de medida', 'type' => 'output'], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_transactions');        
        
    }
}
