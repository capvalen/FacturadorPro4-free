<?php

namespace App\CoreFacturalo\Requests\Api\Transform;

class Functions
{
    public static function valueKeyInArray($inputs, $key, $default = null)
    {
        return array_key_exists($key, $inputs)?$inputs[$key]:$default;
    }
}