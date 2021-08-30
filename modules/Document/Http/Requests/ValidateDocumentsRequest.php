<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateDocumentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'series' => [
                'required',
            ],
            'document_type_id' => [
                'required',
            ],
            'start_number' => [
                'required',
                'numeric',
                'integer',
                'min:1',

            ],
        ];
    }

    
    public function messages()
    {
        return [
            'start_number.required' => 'El campo es obligatorio.',
            'start_number.numeric' => 'El valor debe ser númerico.',
            'start_number.integer' => 'El valor debe ser entero.',
            'start_number.min' => 'El valor minimo es 1.',
        ];
    }
}