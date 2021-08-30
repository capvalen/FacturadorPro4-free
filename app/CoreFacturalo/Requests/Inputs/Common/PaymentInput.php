<?php

namespace App\CoreFacturalo\Requests\Inputs\Common;

class PaymentInput
{
    public static function set($inputs)
    {
        if(array_key_exists('payments', $inputs)) {
            $payments = [];
            foreach ($inputs['payments'] as $row)
            {
                $date_of_payment = $row['date_of_payment'];
                $total_payment = $row['total_payment'];
                $currency_type_id = $row['currency_type_id'];

                $payments[] = [
                    'date_of_payment' => $date_of_payment,
                    'total_payment' => $total_payment,
                    'currency_type_id' => $currency_type_id
                ];
            }

            return $payments;
        }
        return null;
    }
}