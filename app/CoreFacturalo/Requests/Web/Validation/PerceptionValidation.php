<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

class PerceptionValidation
{
    public static function validation($inputs)
    {
        
        $series = Functions::findSeries($inputs);
        if (!$series) throw new Exception("La serie no fue encontrada.");
        $inputs['series'] = $series->number;
        unset($inputs['series_id']);
 
        return $inputs;
    }
}