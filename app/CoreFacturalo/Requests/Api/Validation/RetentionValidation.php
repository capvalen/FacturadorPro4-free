<?php

namespace App\CoreFacturalo\Requests\Api\Validation;

class RetentionValidation
{
    public static function validation($inputs)
    {
        $inputs['establishment_id'] = Functions::establishment($inputs['establishment']);
        unset($inputs['establishment']);

        Functions::validateSeries($inputs);

        $inputs['supplier_id'] = Functions::person($inputs['supplier'], 'suppliers');
        unset($inputs['supplier']);

        return $inputs;
    }
}