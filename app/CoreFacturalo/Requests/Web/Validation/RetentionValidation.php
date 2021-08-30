<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

class RetentionValidation
{
    public static function validation($inputs)
    {
        // $inputs['establishment_id'] = Functions::establishment($inputs['establishment_id']);
        // unset($inputs['establishment']);

        // Functions::validateSeries($inputs);
        
        $series = Functions::findSeries($inputs);
        if (!$series) throw new Exception("La serie no fue encontrada.");
        $inputs['series'] = $series->number;
        unset($inputs['series_id']);

        // $inputs['supplier_id'] = Functions::person($inputs['supplier'], 'supplier');
        // unset($inputs['supplier']);

        return $inputs;
    }
}