<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
     
        return [
            'name' => [
                'required'
            ],
            'description' => [
                'required'
            ],
            'item_id' => [
                'required'
            ],
            'image' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'item_id.required' => 'El campo Producto es obligatorio.',
        ];
    }
}