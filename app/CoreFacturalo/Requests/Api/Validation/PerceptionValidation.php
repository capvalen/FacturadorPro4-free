<?php

namespace App\CoreFacturalo\Requests\Api\Validation;

class PerceptionValidation
{
    public static function validation($inputs)
    {
        $inputs['establishment_id'] = auth()->user()->establishment_id; //Functions::establishment($inputs['establishment']);
//        unset($inputs['establishment']);

        // dd($inputs);
        Functions::validateSeries($inputs);

        $inputs['customer_id'] = Functions::person($inputs['customer'], 'customers');
        unset($inputs['customer']);

        return $inputs;
    }
}