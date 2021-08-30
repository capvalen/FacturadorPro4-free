<?php

namespace App\CoreFacturalo\Requests\Api\Transform\Common;

use App\CoreFacturalo\Requests\Api\Transform\Functions;

class ActionTransform
{
    public static function transform($inputs)
    {
        if(key_exists('acciones', $inputs)) {
            $actions = $inputs['acciones'];
            return [
                'send_email' => Functions::valueKeyInArray($actions, 'enviar_email'),
                'send_xml_signed' => Functions::valueKeyInArray($actions, 'enviar_xml_firmado'),
                'format_pdf' => Functions::valueKeyInArray($actions, 'formato_pdf')
            ];
        }
        return null;
    }
}