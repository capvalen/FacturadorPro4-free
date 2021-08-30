<?php

namespace App\CoreFacturalo\Services\Helpers;

class Functions
{
    public static function verificationCode($number)
    {
        if ($number !== '' || strlen($number) === 8) {
            $hash = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
            $sum = 5;
            for($i = 2; $i < 10; $i++ ) {
                $sum += ($number[$i-2] * $hash[$i]);
            }
            $integer = (int)($sum / 11);
            $digit = 11 - ($sum - $integer * 11);
            if ($digit == 10) {
                $digit = 0;
            } else if ($digit == 11) {
                $digit = 1;
            }
            return $digit;
        }
        return '';
    }
}