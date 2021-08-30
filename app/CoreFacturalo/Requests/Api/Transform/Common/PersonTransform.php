<?php

namespace App\CoreFacturalo\Requests\Api\Transform\Common;

use App\CoreFacturalo\Requests\Api\Transform\Functions;

class PersonTransform
{
    public static function transform($inputs)
    {
        return [
            'identity_document_type_id' => $inputs['codigo_tipo_documento_identidad'],
            'number' => $inputs['numero_documento'],
            'name' => $inputs['apellidos_y_nombres_o_razon_social'],
            'trade_name' => Functions::valueKeyInArray($inputs, 'nombre_comercial'),
            'country_id' => Functions::valueKeyInArray($inputs, 'codigo_pais'),
            'district_id' => Functions::valueKeyInArray($inputs, 'ubigeo'),
            'address' => Functions::valueKeyInArray($inputs, 'direccion'),
            'email' => Functions::valueKeyInArray($inputs, 'correo_electronico'),
            'telephone' => Functions::valueKeyInArray($inputs, 'telefono'),
        ];
    }
}