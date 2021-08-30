<?php

namespace App\CoreFacturalo\Requests\Api\Transform;

use App\CoreFacturalo\Requests\Api\Transform\Common\EstablishmentTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\PersonTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\LegendTransform;
use App\CoreFacturalo\Requests\Api\Transform\Common\ActionTransform;

class DispatchTransform
{
    public static function transform($inputs)
    {
        return  [
            'series' => Functions::valueKeyInArray($inputs, 'serie_documento'),
            'number' => Functions::valueKeyInArray($inputs, 'numero_documento'),
            'date_of_issue' => Functions::valueKeyInArray($inputs, 'fecha_de_emision'),
            'time_of_issue' => Functions::valueKeyInArray($inputs, 'hora_de_emision'),
            'document_type_id' => Functions::valueKeyInArray($inputs, 'codigo_tipo_documento'),
            'establishment' => EstablishmentTransform::transform($inputs['datos_del_emisor']),
            'customer' => PersonTransform::transform($inputs['datos_del_cliente_o_receptor']),
            'observations' => Functions::valueKeyInArray($inputs, 'observaciones'),
            'transport_mode_type_id' => Functions::valueKeyInArray($inputs, 'codigo_modo_transporte'),
            'transfer_reason_type_id' => Functions::valueKeyInArray($inputs, 'codigo_motivo_traslado'),
            'transfer_reason_description' => Functions::valueKeyInArray($inputs, 'descripcion_motivo_traslado'),
            'date_of_shipping' => Functions::valueKeyInArray($inputs, 'fecha_de_traslado'),
            'transshipment_indicator' => Functions::valueKeyInArray($inputs, 'indicador_de_transbordo'),
            'port_code' => Functions::valueKeyInArray($inputs, 'codigo_de_puerto'),
            'unit_type_id' => Functions::valueKeyInArray($inputs, 'unidad_peso_total'),
            'total_weight' => Functions::valueKeyInArray($inputs, 'peso_total'),
            'packages_number' => Functions::valueKeyInArray($inputs, 'numero_de_bultos'),
            'container_number' => Functions::valueKeyInArray($inputs, 'numero_de_contenedor'),
            'license_plate' => Functions::valueKeyInArray($inputs, 'numero_de_placa'),
            'origin' => self::origin($inputs),
            'delivery' => self::delivery($inputs),
            'dispatcher' => self::dispatcher($inputs),
            'driver' => self::driver($inputs),
            'items' => self::items($inputs),
            'legends' => LegendTransform::transform($inputs),
            'actions' => ActionTransform::transform($inputs),

        ];
    }

    private static function origin($inputs)
    {
        if(key_exists('direccion_partida', $inputs)) {
            $origin = $inputs['direccion_partida'];

            return [
                'location_id' => $origin['ubigeo'],
                'address' => $origin['direccion'],
            ];
        }
        return null;
    }

    private static function delivery($inputs)
    {
        if(key_exists('direccion_llegada', $inputs)) {
            $delivery = $inputs['direccion_llegada'];

            return [
                'location_id' => $delivery['ubigeo'],
                'address' => $delivery['direccion'],
            ];
        }
        return null;
    }

    private static function dispatcher($inputs)
    {
        if(key_exists('transportista', $inputs)) {
            $dispatcher = $inputs['transportista'];

            return [
                'identity_document_type_id' => $dispatcher['codigo_tipo_documento_identidad'],
                'number' => $dispatcher['numero_documento'],
                'name' => $dispatcher['apellidos_y_nombres_o_razon_social'],
            ];
        }
        return null;
    }

    private static function driver($inputs)
    {
        $driver = null;
        if(key_exists('chofer', $inputs)) {
            $driver = $inputs['chofer'];

            return [
                'identity_document_type_id' => $driver['codigo_tipo_documento_identidad'],
                'number' => $driver['numero_documento']
            ];
        }
        return null;
    }

    private static function items($inputs)
    {
        if(key_exists('items', $inputs)) {
            $items = [];
            foreach ($inputs['items'] as $row) {
                $items[] = [
                    //'internal_id' => $row['codigo_interno'],
                    //'quantity' => Functions::valueKeyInArray($row, 'cantidad'),

                    'internal_id' => isset($row['codigo_interno']) ? $row['codigo_interno']:'',
                    'description' => Functions::valueKeyInArray($row, 'descripcion'),
                    'name' => Functions::valueKeyInArray($row, 'nombre'),
                    'second_name' => Functions::valueKeyInArray($row, 'nombre_secundario'),
                    'item_type_id' => Functions::valueKeyInArray($row, 'codigo_tipo_item', '01'),
                    'item_code' => Functions::valueKeyInArray($row, 'codigo_producto_sunat'),
                    'item_code_gs1' => Functions::valueKeyInArray($row, 'codigo_producto_gsl'),
                    'unit_type_id' => Functions::valueKeyInArray($row, 'unidad_de_medida'),
                    'currency_type_id' => 'PEN',

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

                    'additional_information' => Functions::valueKeyInArray($row, 'informacion_adicional'),

                ];
            }

            return $items;
        }
        return null;
    }
}
