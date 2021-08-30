<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'soap_type_id' => [
                'nullable'
            ],
            'soap_username' => [
                'required_if:soap_type_id,"02"'
            ],
            'soap_password' => [
                'required_if:soap_type_id,"02"'
            ],
        ];
    }
}
