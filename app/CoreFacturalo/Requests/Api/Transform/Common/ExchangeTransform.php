<?php

namespace App\CoreFacturalo\Requests\Api\Transform\Common;

class ExchangeTransform
{
    public static function transform($inputs)
    {
        if(key_exists('tipo_de_cambio', $inputs)) {
            $exchange_rate = $inputs['tipo_de_cambio'];

            return [
                'currency_type_id_source' => $exchange_rate['codigo_tipo_moneda_referencia'],
                'currency_type_id_target' => $exchange_rate['codigo_tipo_moneda_objetivo'],
                'factor' => $exchange_rate['factor'],
                'date_of_exchange_rate' => $exchange_rate['fecha_tipo_de_cambio'],
            ];
        }
        return null;
    }
}