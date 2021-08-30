<?php

namespace Modules\BusinessTurn\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTransportRequest extends FormRequest
{
    public function authorize() {
        return true;
    }
    
    public function rules() {
        
        return [   
            'seat_number'=> ['required'],
            'passenger_manifest'=> ['required'], 
            'identity_document_type_id'=> ['required'], 
            'number_identity_document'=> ['required'], 
            'passenger_fullname'=> ['required'], 
            'origin_district_id'=> ['required'], 
            'origin_address'=> ['required'], 
            'destinatation_district_id'=> ['required'], 
            'destinatation_address'=> ['required'], 
            'start_date'=> ['required'], 
            'start_time'=> ['required'], 
        ];
    }
}