<?php

namespace App\CoreFacturalo\Requests\Api\Transform\Common;

class PaymentTransform
{
    public static function transform($inputs)
    {
        if(key_exists('pagos', $inputs)) {
            $payments = [];
            foreach ($inputs['pagos'] as $row)
            {
                $payments[] = [
                    'date_of_payment' => $row['fecha_de_pago'],
                    'total_payment' => $row['total_pago'],
                    'currency_type_id' => $row['codigo_tipo_moneda']
                ];
            }

            return $payments;
        }
        return null;
    }
}