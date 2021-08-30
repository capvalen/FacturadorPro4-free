<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigo_tipo_documento' => [
                'required',
            ],
            'serie_documento' => [
                'required',
            ],
            'numero_documento' => [
                'required',
            ],
        ];
    }
}