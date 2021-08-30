<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Configuration;
use Modules\Services\Data\ServiceData;
use App\CoreFacturalo\Services\Ruc\Sunat;
class ServiceController extends Controller
{
	public function ruc($number)
	{
		$configuration = Configuration::first();
		if (!$configuration->token_apiruc || $configuration->token_apiruc === 'false') {
			$service = new Sunat();
			$res     = $service->get($number);
			if ($res) {
				return [
					'success' => true,
					'data'    => [
						'name'       => $res->razonSocial,
						'trade_name' => $res->nombreComercial,
					]
				];
			} else {
				return [
					'success' => false,
					'message' => $service->getError()
				];
			}
		} else {
			try {
				$data     = ServiceData::service('ruc', $number);
				$response = [
					'success' => true,
					'data'    => [
						'name'       => $data['data']['nombre_o_razon_social'],
						'trade_name' => $data['data']['nombre_o_razon_social'],
					]
				];

				return response()->json($response, 200);
			} catch (\Throwable $th) {
				return response()->json([
					'success' => false,
					'message' => 'El número de RUC ingresado no existe. Detalles: ' . $th->getMessage()
				], 200);
			}
		}
	}
}
