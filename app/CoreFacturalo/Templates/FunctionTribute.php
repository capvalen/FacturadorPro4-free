<?php

namespace App\CoreFacturalo\Templates;

class FunctionTribute
{
    private static $code_taxes = [
        '1000' => ['VAT', 'IGV'],
        '1016' => ['VAT', 'IVAP'],
        '2000' => ['EXC', 'ISC'],
        '9995' => ['FRE', 'EXP'],
        '9996' => ['FRE', 'GRA'],
        '9997' => ['VAT', 'EXO'],
        '9998' => ['FRE', 'INA'],
        '9999' => ['OTH', 'OTROS'],
    ];

    public static function getByAffectation($affectation)
    {
        $code = self::getCode($affectation);
        return self::getByTribute($code);
    }

    private static function getByTribute($code)
    {
        if (isset(self::$code_taxes[$code])) {
            $values = self::$code_taxes[$code];
            return [
                'id' => $code,
                'code' => $values[0],
                'name' => $values[1],
            ];
        }
        return null;
    }

    private static function getCode($affectation)
    {
        $value = intval($affectation);
        if ($value === 10) return '1000';
        if ($value === 17) return '1016';
        if ($value === 20) return '9997';
        if ($value === 30) return '9998';
        if ($value === 40) return '9995';
        return '9996';
    }
}