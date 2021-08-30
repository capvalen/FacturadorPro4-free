<?php

namespace Modules\Item\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;
        return [
            'internal_id' => [
                'required',
                'max:30',
                Rule::unique('tenant.items')->ignore($id),
            ],
            'barcode' => [
                'max:150',
            ],
            'model' => [
                'max:100',
            ],
            'has_igv' => 'boolean',
            'item_code' => [
                'max:250',
            ],
            'description' => [
                'required',
                'max:500',
            ],
            'sale_unit_price' => [
                'required',
                'numeric',
                'gt:0'
            ],
            'stock_min' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'description.required' => 'El campo nombre es obligatorio.',
            'sale_unit_price.gt' => 'El precio unitario de venta debe ser mayor que 0.',
        ];
    }
}
