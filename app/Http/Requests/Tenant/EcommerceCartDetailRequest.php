<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EcommerceCartDetailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'telephone' => [
                'required',
            ],
            'address' => [
                'required',
            ]
        ];
    }

    public function messages()
    {
        return [
        'telephone.required' => 'El campo Teléfono es obligatorio.',
        'address.required' => 'El campo Dirección es obligatorio.'
        ];
    }
}
