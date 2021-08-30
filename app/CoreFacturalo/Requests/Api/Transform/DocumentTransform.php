<?php

namespace App\CoreFacturalo\Requests\Api\Transform;

use App\CoreFacturalo\Requests\Api\Transform\Common\EstablishmentTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\PersonTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\ActionTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\LegendTransform;

class DocumentTransform
{
    public static function transform($inputs)
    {

        $totals = $inputs['totales'];

        // foreach ($inputs['items'] as $key => $value) {
        //     $inputs['items'][$key]['codigo_interno'] = ($inputs['items'][$key]['codigo_interno']) ? $inputs['items'][$key]['codigo_interno']:'';
        //     $inputs['items'][$key]['codigo_producto_sunat'] = ($inputs['items'][$key]['codigo_producto_sunat']) ? $inputs['items'][$key]['codigo_producto_sunat']:'';
        // }

        $inputs_transform = [
            'series' => Functions::valueKeyInArray($inputs, 'serie_documento'),
            'number' => Functions::valueKeyInArray($inputs, 'numero_documento'),
            'date_of_issue' => Functions::valueKeyInArray($inputs, 'fecha_de_emision'),
            'time_of_issue' => Functions::valueKeyInArray($inputs, 'hora_de_emision'),
            'document_type_id' => Functions::valueKeyInArray($inputs, 'codigo_tipo_documento'),
            'currency_type_id' => Functions::valueKeyInArray($inputs, 'codigo_tipo_moneda'),
            'exchange_rate_sale' => Functions::valueKeyInArray($inputs, 'factor_tipo_de_cambio', 1),
            'purchase_order' => Functions::valueKeyInArray($inputs, 'numero_orden_de_compra'),
//            'establishment' => EstablishmentTransform::transform($inputs['datos_del_emisor']),
            'customer' => PersonTransform::transform($inputs['datos_del_cliente_o_receptor']),
            'total_prepayment' => Functions::valueKeyInArray($totals, 'total_anticipos'),
            'total_discount' => Functions::valueKeyInArray($totals, 'total_descuentos'),
            'total_charge' => Functions::valueKeyInArray($totals, 'total_cargos'),
            'total_exportation' => Functions::valueKeyInArray($totals, 'total_exportacion'),
            'total_free' => Functions::valueKeyInArray($totals, 'total_operaciones_gratuitas'),
            'total_taxed' => Functions::valueKeyInArray($totals, 'total_operaciones_gravadas'),
            'total_unaffected' => Functions::valueKeyInArray($totals, 'total_operaciones_inafectas'),
            'total_exonerated' => Functions::valueKeyInArray($totals, 'total_operaciones_exoneradas'),
            'total_igv' => Functions::valueKeyInArray($totals, 'total_igv'),
            'total_base_isc' => Functions::valueKeyInArray($totals, 'total_base_isc'),
            'total_isc' => Functions::valueKeyInArray($totals, 'total_isc'),
            'total_base_other_taxes' => Functions::valueKeyInArray($totals, 'total_base_otros_impuestos'),
            'total_other_taxes' => Functions::valueKeyInArray($totals, 'total_otros_impuestos'),
            'total_plastic_bag_taxes' => Functions::valueKeyInArray($totals, 'total_impuestos_bolsa_plastica'),
            'total_taxes' => Functions::valueKeyInArray($totals, 'total_impuestos'),
            'total_value' => Functions::valueKeyInArray($totals, 'total_valor'),
            'total' => Functions::valueKeyInArray($totals, 'total_venta'),
            'has_prepayment' => Functions::valueKeyInArray($inputs, 'pago_anticipado',0),
            'items' => self::items($inputs),
            'charges' => self::charges($inputs),
            'discounts' => self::discounts($inputs),
            'detraction' => self::detraction($inputs),
            'perception' => self::perception($inputs),
            'prepayments' => self::prepayments($inputs),
            'guides' => self::guides($inputs),
            'related' => self::related($inputs),
            'legends' => LegendTransform::transform($inputs),
            'additional_information' => Functions::valueKeyInArray($inputs, 'informacion_adicional'),
            'actions' => ActionTransform::transform($inputs),
            'hotel' => Functions::valueKeyInArray($inputs, 'hotel',[]),
            'transport' => Functions::valueKeyInArray($inputs, 'transport',[]),
            'payments' => self::payments($inputs),
            'data_json' => $inputs,
            'fee' => self::fee($inputs),
            'payment_condition_id' => Functions::valueKeyInArray($inputs, 'codigo_condicion_de_pago', '01'),
            'sale_note_id' => Functions::valueKeyInArray($inputs, 'codigo_nota_venta')
        ];

        $inputs_transform = self::invoice($inputs_transform, $inputs);
        $inputs_transform = self::note($inputs_transform, $inputs);

        return $inputs_transform;
    }


    private static function items($inputs)
    {
        if(key_exists('items', $inputs)) {
            $items = [];
            foreach ($inputs['items'] as $row) {
                $items[] = [
                    'internal_id' => isset($row['codigo_interno']) ? $row['codigo_interno']:'',
                    'description' => $row['descripcion'],
                    'name' => Functions::valueKeyInArray($row, 'nombre'),
                    'second_name' => Functions::valueKeyInArray($row, 'nombre_secundario'),
                    'item_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_item', '01'),
                    'item_code' => Functions::valueKeyInArray($row, 'codigo_producto_sunat'),
                    'item_code_gs1' => Functions::valueKeyInArray($row, 'codigo_producto_gsl'),
                    'unit_type_id' => strtoupper($row['unidad_de_medida']),
                    'currency_type_id' => $inputs['codigo_tipo_moneda'],

                    'quantity' => Functions::valueKeyInArray($row, 'cantidad'),
                    'unit_value' => Functions::valueKeyInArray($row, 'valor_unitario'),
                    'price_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_precio'),
                    'unit_price' => Functions::valueKeyInArray($row, 'precio_unitario'),

                    'affectation_igv_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_afectacion_igv'),
                    'total_base_igv' => Functions::valueKeyInArray($row, 'total_base_igv'),
                    'percentage_igv' => Functions::valueKeyInArray($row, 'porcentaje_igv'),
                    'total_igv' => Functions::valueKeyInArray($row, 'total_igv'),

                    'system_isc_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_sistema_isc'),
                    'total_base_isc' => Functions::valueKeyInArray($row, 'total_base_isc'),
                    'percentage_isc' => Functions::valueKeyInArray($row, 'porcentaje_isc'),
                    'total_isc' => Functions::valueKeyInArray($row, 'total_isc'),

                    'total_base_other_taxes' => Functions::valueKeyInArray($row, 'total_base_otros_impuestos'),
                    'percentage_other_taxes' => Functions::valueKeyInArray($row, 'porcentaje_otros_impuestos'),
                    'total_other_taxes' => Functions::valueKeyInArray($row, 'total_otros_impuestos'),
                    'total_plastic_bag_taxes' => Functions::valueKeyInArray($row, 'total_impuestos_bolsa_plastica'),

                    'total_taxes' => Functions::valueKeyInArray($row, 'total_impuestos'),
                    'total_value' => Functions::valueKeyInArray($row, 'total_valor_item'),
                    'total_charge' => Functions::valueKeyInArray($row, 'total_cargos'),
                    'total_discount' => Functions::valueKeyInArray($row, 'total_descuentos'),
                    'total' => Functions::valueKeyInArray($row, 'total_item'),

                    'attributes' => self::attributes($row),
                    'discounts' => self::discounts($row),
                    'charges' => self::charges($row),
                    'additional_information' => Functions::valueKeyInArray($row, 'informacion_adicional'),
                    'lots' => Functions::valueKeyInArray($row, 'lots', []),
                ];
            }

            return $items;
        }
        return null;
    }

    private static function attributes($inputs)
    {
        if(key_exists('datos_adicionales', $inputs)) {
            $attributes = [];
            foreach ($inputs['datos_adicionales'] as $row)
            {
                $attributes[] = [
                    'attribute_type_id' => $row['codigo'],
                    'description' => $row['descripcion'],
                    'value' => Functions::valueKeyInArray($row, 'valor'),
                    'start_date' => Functions::valueKeyInArray($row, 'fecha_inicio'),
                    'end_date' => Functions::valueKeyInArray($row, 'fecha_fin'),
                    'duration' => Functions::valueKeyInArray($row, 'duracion'),
                ];
            }

            return $attributes;
        }
        return null;
    }

    private static function charges($inputs)
    {
        if(key_exists('cargos', $inputs)) {
            $charges = [];
            foreach ($inputs['cargos'] as $row)
            {
                $charges[] = [
                    'charge_type_id' => $row['codigo'],
                    'description' => $row['descripcion'],
                    'factor' => $row['factor'],
                    'amount' => $row['monto'],
                    'base' =>  $row['base'],
                ];
            }

            return $charges;
        }
        return null;
    }

    private static function discounts($inputs)
    {
        if(key_exists('descuentos', $inputs)) {
            $discounts = [];
            foreach ($inputs['descuentos'] as $row) {
                $discounts[] = [
                    'discount_type_id' => $row['codigo'],
                    'description' => $row['descripcion'],
                    'factor' => $row['factor'],
                    'amount' => $row['monto'],
                    'base' =>  $row['base'],
                ];
            }

            return $discounts;
        }
        return null;
    }

    private static function detraction($inputs)
    {
        if(key_exists('detraccion', $inputs)) {
            $detraction = $inputs['detraccion'];
            return [
                'detraction_type_id' => $detraction['codigo_tipo_detraccion'],
                'percentage' => $detraction['porcentaje'],
                'amount' => $detraction['monto'],
                'payment_method_id' => $detraction['codigo_metodo_pago'],
                'bank_account' => $detraction['cuenta_bancaria'],
            ];
        }
        return null;
    }

    private static function perception($inputs)
    {
        if(key_exists('percepcion', $inputs)) {
            $perception = $inputs['percepcion'];

            return [
                'code' => $perception['codigo'],
                'percentage' => $perception['porcentaje'],
                'amount' => $perception['monto'],
                'base' => $perception['base'],
            ];
        }
        return null;
    }

    private static function prepayments($inputs)
    {
        if(key_exists('anticipos', $inputs)) {
            $prepayments = [];
            foreach ($inputs['anticipos'] as $row)
            {
                $prepayments[] = [
                    'number' => $row['numero'],
                    'document_type_id' => $row['codigo_tipo_documento'],
                    'amount' => $row['monto'],
                    'total' => $row['total']
                ];
            }

            return $prepayments;
        }
        return null;
    }

    private static function guides($inputs)
    {
        if(key_exists('guias', $inputs)) {
            $guides = [];
            foreach ($inputs['guias'] as $row)
            {
                $guides[] = [
                    'number' => $row['numero'],
                    'document_type_id' => $row['codigo_tipo_documento'],
                ];
            }

            return $guides;
        }
        return null;
    }

    private static function related($inputs)
    {
        if(key_exists('relacionados', $inputs)) {
            $related = [];
            foreach ($inputs['relacionados'] as $row)
            {
                $related[] = [
                    'number' => $row['numero'],
                    'document_type_id' => $row['codigo_tipo_documento'],
                    'amount' => $row['monto']
                ];
            }

            return $related;
        }
        return null;
    }

    private static function invoice($inputs_transform, $inputs)
    {
        if(in_array($inputs['codigo_tipo_documento'], ['01', '03'])) {
            $inputs_transform['operation_type_id'] = Functions::valueKeyInArray($inputs, 'codigo_tipo_operacion');
            $inputs_transform['date_of_due'] = Functions::valueKeyInArray($inputs, 'fecha_de_vencimiento');
        }
        return $inputs_transform;
    }

    private static function note($inputs_transform, $inputs)
    {
        if(in_array($inputs['codigo_tipo_documento'], ['07', '08'])) {
            $inputs_transform['note_credit_or_debit_type_id'] = Functions::valueKeyInArray($inputs, 'codigo_tipo_nota');
            $inputs_transform['note_description'] = Functions::valueKeyInArray($inputs, 'motivo_o_sustento_de_nota');
            $inputs_transform['affected_document_external_id'] = Functions::valueKeyInArray($inputs['documento_afectado'], 'external_id');

            if(!$inputs_transform['affected_document_external_id']){


                $inputs_transform['data_affected_document']['number'] = Functions::valueKeyInArray($inputs['documento_afectado'], 'numero_documento');
                $inputs_transform['data_affected_document']['series'] = Functions::valueKeyInArray($inputs['documento_afectado'], 'serie_documento');
                $inputs_transform['data_affected_document']['document_type_id'] = Functions::valueKeyInArray($inputs['documento_afectado'], 'codigo_tipo_documento');

                // dd($inputs);
            }
        }
        return $inputs_transform;
    }


    private static function payments($inputs)
    {
        if(in_array($inputs['codigo_tipo_documento'], ['01', '03'])) {

            $payments = [];

            if(key_exists('pagos', $inputs)) {

                foreach ($inputs['pagos'] as $row) {
                    $payments[] = [
                        'date_of_payment' => Functions::valueKeyInArray($inputs, 'fecha_de_emision'),
                        'payment_method_type_id' => $row['codigo_metodo_pago'],
                        'payment_destination_id' => $row['codigo_destino_pago'],
                        'reference' => Functions::valueKeyInArray($row, 'referencia'),
                        'payment' => Functions::valueKeyInArray($row, 'monto', 0),
                    ];
                }

            }

            return $payments;

        }

        return [];
    }

    private static function fee($inputs)
    {
        $fee = [];
        if (key_exists('cuotas', $inputs)) {
            foreach ($inputs['cuotas'] as $row) {
                $fee[] = [
                    'date' => $row['fecha'],
                    'currency_type_id' => $row['codigo_tipo_moneda'],
                    'amount' => $row['monto'],
                ];
            }
        }

        return $fee;
    }
}
