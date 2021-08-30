<?php

namespace App\CoreFacturalo\Requests\Api\Validation;

use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use Exception;

class SummaryValidation
{
    public static function validation($inputs)
    {
        if($inputs['summary_status_type_id'] === '3') {
            $inputs['documents'] = Functions::voidedDocuments($inputs, 'summary');
        } else {
            $inputs['documents'] = self::findDocuments($inputs);
        }
        return $inputs;
    }

    private static function findDocuments($inputs)
    {
        $company = Company::active();
        $documents = Document::where('date_of_issue', $inputs['date_of_reference'])
                            ->where('soap_type_id', $company->soap_type_id)
                            ->where('group_id', '02')
                            ->where('state_type_id', '01')
                            ->take(500)
                            ->get();

        if($documents->count() === 0) {
            throw new Exception("No se encontraron documentos con fecha de emisión {$inputs['date_of_reference']}.");
        }

        $docs = [];
        foreach ($documents as $row)
        {
            $docs[] = [
                'document_id' => $row->id
            ];
        }
        return $docs;
    }
}