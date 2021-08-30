<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DetractionTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'id' => [
                'required',
                Rule::unique('tenant.cat_detraction_types')->ignore($id),
            ],
            'description' => [
                'required',
            ],
            'operation_type_id' => [
                'required',
            ],
            'percentage' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'active' => [
                'required',
            ],
        ];
    }
}