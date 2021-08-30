<?php

namespace App\CoreFacturalo\Requests\Inputs\Transform;

use App\CoreFacturalo\Requests\Api\Transform\Common\EstablishmentTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\PersonTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\ActionTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\LegendTransform;
use App\CoreFacturalo\Requests\Api\Transform\Functions;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Models\Tenant\Item;
use App\Models\Tenant\Document;

class DocumentWebTransform
{
    public static function transform($inputs)
    {

        // dd($inputs);
        $customer = PersonInput::set($inputs['customer_id']);

        $inputs_transform = [
            'serie_documento' => Functions::valueKeyInArray($inputs, 'series'),
            'numero_documento' => Functions::valueKeyInArray($inputs, 'number'),
            'fecha_de_emision' => Functions::valueKeyInArray($inputs, 'date_of_issue'),
            'hora_de_emision' => Functions::valueKeyInArray($inputs, 'time_of_issue'),
            'codigo_tipo_documento' => Functions::valueKeyInArray($inputs, 'document_type_id'),
            'codigo_tipo_moneda' => Functions::valueKeyInArray($inputs, 'currency_type_id'),
            'factor_tipo_de_cambio' => Functions::valueKeyInArray($inputs, 'exchange_rate_sale', 1),
            'numero_orden_de_compra' => Functions::valueKeyInArray($inputs, 'purchase_order'),
//            'establishment' => EstablishmentTransform::transform($inputs['datos_del_emisor']),
            'datos_del_cliente_o_receptor' => self::person_transform($customer),

            'totales' => [

                'total_anticipos' => Functions::valueKeyInArray($inputs, 'total_prepayment'),
                'total_descuentos' => Functions::valueKeyInArray($inputs, 'total_discount'),
                'total_cargos' => Functions::valueKeyInArray($inputs, 'total_charge'),
                'total_exportacion' => Functions::valueKeyInArray($inputs, 'total_exportation'),
                'total_operaciones_gratuitas' => Functions::valueKeyInArray($inputs, 'total_free'),
                'total_operaciones_gravadas' => Functions::valueKeyInArray($inputs, 'total_taxed'),
                'total_operaciones_inafectas' => Functions::valueKeyInArray($inputs, 'total_unaffected'),
                'total_operaciones_exoneradas' => Functions::valueKeyInArray($inputs, 'total_exonerated'),
                'total_igv' => Functions::valueKeyInArray($inputs, 'total_igv'),
                'total_base_isc' => Functions::valueKeyInArray($inputs, 'total_base_isc'),
                'total_isc' => Functions::valueKeyInArray($inputs, 'total_isc'),
                'total_base_otros_impuestos' => Functions::valueKeyInArray($inputs, 'total_base_other_taxes'),
                'total_otros_impuestos' => Functions::valueKeyInArray($inputs, 'total_other_taxes'),
                'total_impuestos_bolsa_plastica' => Functions::valueKeyInArray($inputs, 'total_plastic_bag_taxes'),
                'total_impuestos' => Functions::valueKeyInArray($inputs, 'total_taxes'),
                'total_valor' => Functions::valueKeyInArray($inputs, 'total_value'),
                'total_venta' => Functions::valueKeyInArray($inputs, 'total'),
            ],

            // 'pago_anticipado' => Functions::valueKeyInArray($inputs, 'has_prepayment',0),
            'items' => self::items($inputs),
            // 'charges' => self::charges($inputs),
            // 'discounts' => self::discounts($inputs),
            // 'detraction' => self::detraction($inputs),
            // 'perception' => self::perception($inputs),
            // 'prepayments' => self::prepayments($inputs),
            // 'guides' => self::guides($inputs),
            // 'related' => self::related($inputs),
            // 'legends' => LegendTransform::transform($inputs),
            'informacion_adicional' => Functions::valueKeyInArray($inputs, 'additional_information'),
            'acciones' => self::actions_transform($inputs),
            // 'hotel' => Functions::valueKeyInArray($inputs, 'hotel',[]),
            'pagos' => self::payments($inputs),
            // 'data_json' => $inputs
        ];

        $inputs_transform = self::invoice($inputs_transform, $inputs);
        $inputs_transform = self::note($inputs_transform, $inputs);
        // dd($inputs_transform);
        
        return $inputs_transform;
    }

    public static function actions_transform($inputs){

        if(key_exists('actions', $inputs)) {
            $actions = $inputs['actions'];
            return [ 
                'formato_pdf' => Functions::valueKeyInArray($actions, 'format_pdf')
            ];
        }
        return null;
    }


    public static function person_transform($person)
    {
        return [
            'codigo_tipo_documento_identidad' => $person['identity_document_type_id'],
            'numero_documento' => $person['number'],
            'apellidos_y_nombres_o_razon_social' => $person['name'],
            'nombre_comercial' => Functions::valueKeyInArray($person, 'trade_name'),
            'codigo_pais' => Functions::valueKeyInArray($person, 'country_id'),
            'ubigeo' => Functions::valueKeyInArray($person, 'district_id'),
            'direccion' => Functions::valueKeyInArray($person, 'address'),
            'correo_electronico' => Functions::valueKeyInArray($person, 'email'),
            'telefono' => Functions::valueKeyInArray($person, 'telephone'),
        ];
    }

    private static function items($inputs)
    {
        if(key_exists('items', $inputs)) {
            $items = [];
            foreach ($inputs['items'] as $row) {
                $item = Item::find($row['item_id']);

                $items[] = [
                    'codigo_interno' => isset($row['item']['internal_id']) ? $row['item']['internal_id']:'',
                    'descripcion' => $row['item']['description'],
                    // 'nombre' => Functions::valueKeyInArray($row['item'], 'name'),
                    // 'nombre_secundario' => Functions::valueKeyInArray($row['item'], 'second_name'),
                    'codigo_tipo_item' => $item->item_type_id,
                    'codigo_producto_sunat' => trim(($item->item_code) ? $item->item_code:''),
                    // 'codigo_producto_gsl' => $item->item_code_gs1,
                    'unidad_de_medida' => strtoupper($row['item']['unit_type_id']),
                    'codigo_tipo_moneda' => $inputs['currency_type_id'],

                    'cantidad' => Functions::valueKeyInArray($row, 'quantity'),
                    'valor_unitario' => Functions::valueKeyInArray($row, 'unit_value'),
                    'codigo_tipo_precio' => Functions::valueKeyInArray($row, 'price_type_id'),
                    'precio_unitario' => Functions::valueKeyInArray($row, 'unit_price'),

                    'codigo_tipo_afectacion_igv' => Functions::valueKeyInArray($row, 'affectation_igv_type_id'),
                    'total_base_igv' => Functions::valueKeyInArray($row, 'total_base_igv'),
                    'porcentaje_igv' => Functions::valueKeyInArray($row, 'percentage_igv'),
                    'total_igv' => Functions::valueKeyInArray($row, 'total_igv'),

                    'codigo_tipo_sistema_isc' => Functions::valueKeyInArray($row, 'system_isc_type_id'),
                    'total_base_isc' => Functions::valueKeyInArray($row, 'total_base_isc'),
                    'porcentaje_isc' => Functions::valueKeyInArray($row, 'percentage_isc'),
                    'total_isc' => Functions::valueKeyInArray($row, 'total_isc'),

                    'total_base_otros_impuestos' => Functions::valueKeyInArray($row, 'total_base_other_taxes'),
                    'porcentaje_otros_impuestos' => Functions::valueKeyInArray($row, 'percentage_other_taxes'),
                    'total_otros_impuestos' => Functions::valueKeyInArray($row, 'total_other_taxes'),
                    'total_impuestos_bolsa_plastica' => Functions::valueKeyInArray($row, 'total_plastic_bag_taxes'),

                    'total_impuestos' => Functions::valueKeyInArray($row, 'total_taxes'),
                    'total_valor_item' => Functions::valueKeyInArray($row, 'total_value'),
                    'total_cargos' => Functions::valueKeyInArray($row, 'total_charge'),
                    'total_descuentos' => Functions::valueKeyInArray($row, 'total_discount'),
                    'total_item' => Functions::valueKeyInArray($row, 'total'),

                    // 'attributes' => self::attributes($row),
                    // 'discounts' => self::discounts($row),
                    // 'charges' => self::charges($row),
                ];
            }

            return $items;
        }
        return null;
    }


    

    private static function invoice($inputs_transform, $inputs)
    {
        if(in_array($inputs['document_type_id'], ['01', '03'])) {
            $inputs_transform['codigo_tipo_operacion'] = Functions::valueKeyInArray($inputs, 'operation_type_id');
            $inputs_transform['fecha_de_vencimiento'] = Functions::valueKeyInArray($inputs, 'date_of_due');
        }
        return $inputs_transform;
    }

    private static function note($inputs_transform, $inputs)
    {
        if(in_array($inputs['document_type_id'], ['07', '08'])) {
            $inputs_transform['codigo_tipo_nota'] = Functions::valueKeyInArray($inputs, 'note_credit_or_debit_type_id');
            $inputs_transform['motivo_o_sustento_de_nota'] = Functions::valueKeyInArray($inputs, 'note_description');
            $inputs_transform['documento_afectado'] = [
                'external_id' => Document::select('external_id')->find($inputs['affected_document_id'])->external_id
            ]; 
        }
        return $inputs_transform;
    }



    private static function attributes($inputs)
    {
        // if(key_exists('datos_adicionales', $inputs)) {
        //     $attributes = [];
        //     foreach ($inputs['datos_adicionales'] as $row)
        //     {
        //         $attributes[] = [
        //             'attribute_type_id' => $row['codigo'],
        //             'description' => $row['descripcion'],
        //             'value' => Functions::valueKeyInArray($row, 'valor'),
        //             'start_date' => Functions::valueKeyInArray($row, 'fecha_inicio'),
        //             'end_date' => Functions::valueKeyInArray($row, 'fecha_fin'),
        //             'duration' => Functions::valueKeyInArray($row, 'duracion'),
        //         ];
        //     }

        //     return $attributes;
        // }
        return null;
    }

    private static function charges($inputs)
    {
        // if(key_exists('cargos', $inputs)) {
        //     $charges = [];
        //     foreach ($inputs['cargos'] as $row)
        //     {
        //         $charges[] = [
        //             'charge_type_id' => $row['codigo'],
        //             'description' => $row['descripcion'],
        //             'factor' => $row['factor'],
        //             'amount' => $row['monto'],
        //             'base' =>  $row['base'],
        //         ];
        //     }

        //     return $charges;
        // }
        return null;
    }

    private static function discounts($inputs)
    {
        // if(key_exists('descuentos', $inputs)) {
        //     $discounts = [];
        //     foreach ($inputs['descuentos'] as $row) {
        //         $discounts[] = [
        //             'discount_type_id' => $row['codigo'],
        //             'description' => $row['descripcion'],
        //             'factor' => $row['factor'],
        //             'amount' => $row['monto'],
        //             'base' =>  $row['base'],
        //         ];
        //     }

        //     return $discounts;
        // }
        return null;
    }

    private static function detraction($inputs)
    {
        // if(key_exists('detraccion', $inputs)) {
        //     $detraction = $inputs['detraccion'];
        //     return [
        //         'code' => $detraction['codigo'],
        //         'percentage' => $detraction['porcentaje'],
        //         'amount' => $detraction['monto'],
        //         'payment_method_id' => $detraction['codigo_metodo_pago'],
        //         'bank_account' => $detraction['cuenta_bancaria'],
        //     ];
        // }
        return null;
    }

    private static function perception($inputs)
    {
        // if(key_exists('percepcion', $inputs)) {
        //     $perception = $inputs['percepcion'];

        //     return [
        //         'code' => $perception['codigo'],
        //         'percentage' => $perception['porcentaje'],
        //         'amount' => $perception['monto'],
        //         'base' => $perception['base'],
        //     ];
        // }
        return null;
    }

    private static function prepayments($inputs)
    {
        // if(key_exists('anticipos', $inputs)) {
        //     $prepayments = [];
        //     foreach ($inputs['anticipos'] as $row)
        //     {
        //         $prepayments[] = [
        //             'number' => $row['numero'],
        //             'document_type_id' => $row['codigo_tipo_documento'],
        //             'amount' => $row['monto'],
        //             'total' => $row['total']
        //         ];
        //     }

        //     return $prepayments;
        // }
        return null;
    }

    private static function guides($inputs)
    {
        // if(key_exists('guias', $inputs)) {
        //     $guides = [];
        //     foreach ($inputs['guias'] as $row)
        //     {
        //         $guides[] = [
        //             'number' => $row['numero'],
        //             'document_type_id' => $row['codigo_tipo_documento'],
        //         ];
        //     }

        //     return $guides;
        // }
        return null;
    }

    private static function related($inputs)
    {
        // if(key_exists('relacionados', $inputs)) {
        //     $related = [];
        //     foreach ($inputs['relacionados'] as $row)
        //     {
        //         $related[] = [
        //             'number' => $row['numero'],
        //             'document_type_id' => $row['codigo_tipo_documento'],
        //             'amount' => $row['monto']
        //         ];
        //     }

        //     return $related;
        // }
        return null;
    }

    private static function payments($inputs)
    {
        if(in_array($inputs['document_type_id'], ['01', '03'])) {

            $payments = [];

            if(key_exists('payments', $inputs)) {

                foreach ($inputs['payments'] as $row) {
                    $payments[] = [
                        'fecha_de_emision' => $row['date_of_payment'],
                        'codigo_metodo_pago' => $row['payment_method_type_id'],
                        'codigo_destino_pago' => $row['payment_destination_id'],
                        'referencia' => Functions::valueKeyInArray($row, 'reference'), 
                        'monto' => Functions::valueKeyInArray($row, 'payment', 0), 
                    ];
                } 

            }

            return $payments;

        } 

        return [];
    }

}