<?php

namespace App\CoreFacturalo\Requests\Inputs\Common;

use App\CoreFacturalo\Requests\Inputs\Functions;
use App\Models\Tenant\Configuration;

class ActionInput
{
    public static function set($inputs)
    {
        $actions = [];
        if(array_key_exists('actions', $inputs)) {
           if($inputs['actions']) {
               $actions = $inputs['actions'];
           }
        }

        $configuration = Configuration::first();

        return [
            'send_email' => self::sendEmail($actions, $inputs, $configuration),
            'send_xml_signed' => self::sendXmlSigned($actions, $inputs, $configuration),
            'format_pdf' => self::formatPdf($actions, $inputs, $configuration),
        ];
    }

    private static function sendEmail($actions, $inputs, $configuration)
    {
        return Functions::valueKeyInArray($actions, 'send_email', false);
    }

    private static function sendXmlSigned($actions, $inputs, $configuration)
    {
        $send_xml_signed = Functions::valueKeyInArray($actions, 'send_xml_signed', true);
        if(in_array($inputs['type'], ['invoice', 'credit', 'debit'])) {
            if($inputs['group_id'] === '02') {
                return false;
            }
            return $configuration->send_auto && $send_xml_signed;
        }

        return true;
    }

    private static function formatPdf($actions, $inputs, $configuration)
    {
        return Functions::valueKeyInArray($actions, 'format_pdf', 'a4');
    }
}