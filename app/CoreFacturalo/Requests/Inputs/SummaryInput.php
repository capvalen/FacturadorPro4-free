<?php

namespace App\CoreFacturalo\Requests\Inputs;

use Illuminate\Support\Str;
use App\Models\Tenant\{
    Company,
    Summary
};

class SummaryInput
{
    public static function set($inputs) {
        $company = Company::active();
        $soap_type_id = $company->soap_type_id;
        
        $date_of_reference = $inputs['date_of_reference'];
        $date_of_issue = date('Y-m-d');
        $summary_status_type_id = $inputs['summary_status_type_id'];
        
        $identifier = Functions::identifier($soap_type_id, $date_of_issue, Summary::class);
        $filename = $company->number.'-'.$identifier;
        $inputs['type'] = 'summary';
        
        return [
            'type' => $inputs['type'],
            'user_id' => auth()->id(),
            'external_id' => Str::uuid(),
            'soap_type_id' => $soap_type_id,
            'state_type_id' => '01',
            'summary_status_type_id' => $summary_status_type_id,
            'ubl_version' => '2.0',
            'date_of_issue' => $date_of_issue,
            'date_of_reference' => $date_of_reference,
            'identifier' => $identifier,
            'filename' => $filename,
            'documents' => $inputs['documents']
        ];
    }
}
