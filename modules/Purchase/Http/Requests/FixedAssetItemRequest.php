<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FixedAssetItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'internal_id' => [
                'nullable',
                Rule::unique('tenant.fixed_asset_items')->ignore($id),
            ],
            'name' => [
                'required',
            ], 
            'unit_type_id' => [
                'required',
            ],
            'currency_type_id' => [
                'required'
            ],
            'purchase_unit_price' => [
                'required',
                'numeric',
                'gt:0'
            ], 
            'purchase_affectation_igv_type_id' => [
                'required'
            ], 
            
        ];
    }

    public function messages()
    {
        return [
            'purchase_unit_price.gt' => 'El precio unitario de compra debe ser mayor que 0.',
        ];
    }
}