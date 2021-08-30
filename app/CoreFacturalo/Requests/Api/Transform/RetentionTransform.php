<?php

namespace App\CoreFacturalo\Requests\Api\Transform;

use App\CoreFacturalo\Requests\Api\Transform\Common\ActionTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\EstablishmentTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\ExchangeTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\LegendTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\PaymentTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\PersonTransform;

class RetentionTransform
{
    public static function transform($inputs)
    {
        $totals = $inputs['totales'];
        return [
            'series' => Functions::valueKeyInArray($inputs, 'serie_documento'),
            'number' => Functions::valueKeyInArray($inputs, 'numero_documento'),
            'date_of_issue' => Functions::valueKeyInArray($inputs, 'fecha_de_emision'),
            'time_of_issue' => Functions::valueKeyInArray($inputs, 'hora_de_emision'),
            'document_type_id' => Functions::valueKeyInArray($inputs, 'codigo_tipo_documento'),
            'establishment' => EstablishmentTransform::transform($inputs['datos_del_emisor']),
            'supplier' => PersonTransform::transform($inputs['datos_del_proveedor']),
            'retention_type_id' => Functions::valueKeyInArray($inputs, 'codigo_tipo_retencion'),
            'observations' => Functions::valueKeyInArray($inputs, 'observaciones'),
            'total_retention' => Functions::valueKeyInArray($totals, 'total_retenido'),
            'total' => Functions::valueKeyInArray($totals, 'total_pagado'),
            'documents' => self::document($inputs),
            'legends' => LegendTransform::transform($inputs),
            'actions' => ActionTransform::transform($inputs),
        ];
    }

    private static function document($inputs)
    {
        if(key_exists('documentos', $inputs)) {
            $documents = [];
            foreach ($inputs['documentos'] as $row)
            {
                $documents[] = [
                    'document_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_documento'),
                    'series' => Functions::valueKeyInArray($row, 'serie_documento'),
                    'number' => Functions::valueKeyInArray($row, 'numero_documento'),
                    'date_of_issue' => Functions::valueKeyInArray($row, 'fecha_de_emision'),
                    'currency_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_moneda'),
                    'total_document' => Functions::valueKeyInArray($row, 'total_documento'),
                    'payments' => PaymentTransform::transform($row),
                    'exchange_rate' => ExchangeTransform::transform($row),
                    'date_of_retention' => Functions::valueKeyInArray($row, 'fecha_de_retencion'),
                    'total_retention' => Functions::valueKeyInArray($row, 'total_retenido'),
                    'total_to_pay' => Functions::valueKeyInArray($row, 'total_a_pagar'),
                    'total_payment' => Functions::valueKeyInArray($row, 'total_pagado'),
                ];
            }

            return $documents;
        }
        return null;
    }
}