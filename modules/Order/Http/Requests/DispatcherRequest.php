<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispatcherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->input('id');

        return [
            'number' => [
                'required',
                Rule::unique('tenant.dispatchers')->ignore($id),
            ],
            'identity_document_type_id' => [
                'required',
            ], 
            'name' => [
                'required',
            ],
            'address' => [
                'required'
            ], 
            
        ];
    }

}