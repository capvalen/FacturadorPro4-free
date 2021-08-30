<?php

namespace Modules\BusinessTurn\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentHotelGuestRequest  extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {

        return [
            // 'number'=> ['required','numeric','integer'],
            'number'=> ['required','numeric'],
            'name'=> ['required'],
            'identity_document_type_id'=> ['required'],
            'sex'=> ['required'],
            'age'=> ['required','numeric','integer'],
            'civil_status'=> ['required'],
            'nacionality'=> ['required'],
            'origin'=> ['required'],
            'ocupation'=> ['required'],
        ];
    }
}
