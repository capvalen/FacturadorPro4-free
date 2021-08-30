<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

class VoidedValidation
{
    public static function validation($inputs)
    {
        //$inputs['documents'] = self::setDocuments($inputs);
        return $inputs;
    }

    private static function setDocuments($inputs)
    {
        $docs = [];
        foreach ($inputs['documents'] as $row)
        {
            $docs[] = [
                'document_id' => $row['id']
            ];
        }
        return $docs;
    }
}