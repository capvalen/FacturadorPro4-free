<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

class SummaryValidation
{
    public static function validation($inputs) {
        if ($inputs['summary_status_type_id'] === '1') $inputs['documents'] = self::setDocuments($inputs);
        
        return $inputs;
    }
    
    private static function setDocuments($inputs) {
        $docs = [];
        
        foreach ($inputs['documents'] as $row) $docs[] = ['document_id' => $row['id']];
        
        return $docs;
    }
}