<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleLevelsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('module_levels', function (Blueprint $table) {
			$table->increments('id');
			$table->string('value');
			$table->string('description');
			$table->unsignedInteger('module_id');
			$table->foreign('module_id')->references('id')->on('modules');
			$table->timestamps();
		});
		DB::table('module_levels')->insert([
			['value' => 'new_document', 'description' => 'Nuevo comprobante', 'module_id' => 1],
            ['value' => 'list_document', 'description' => 'L. Comprobantes', 'module_id' => 1],
			['value' => 'document_not_sent', 'description' => 'Doc. No enviados', 'module_id' => 1],
			['value' => 'document_contingengy', 'description' => 'Doc. Contingencia', 'module_id' => 1],
			['value' => 'catalogs', 'description' => 'Catálogos', 'module_id' => 1],
			['value' => 'summary_voided', 'description' => 'Resúmenes y Anulaciones', 'module_id' => 1],
			['value' => 'quotations', 'description' => 'Cotizaciones', 'module_id' => 1],
			['value' => 'sale_notes', 'description' => 'Notas de Venta', 'module_id' => 1],
			['value' => 'incentives', 'description' => 'Comisiones', 'module_id' => 1],
			['value' => 'sale-opportunity', 'description' => 'Oportunidad de venta', 'module_id' => 1],
			['value' => 'contracts', 'description' => 'Contratos', 'module_id' => 1],
			['value' => 'order-note', 'description' => 'Pedidos', 'module_id' => 1],
			['value' => 'technical-service', 'description' => 'Servicios de soporte técnico', 'module_id' => 1],
            ['value' => 'regularize_shipping', 'description' => 'CPE pendientes de rectificación', 'module_id' => 1]
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('module_levels');
	}
}
