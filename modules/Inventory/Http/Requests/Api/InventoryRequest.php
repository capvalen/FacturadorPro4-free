<?php

namespace Modules\Inventory\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'item_code' => [
                'required',
            ],
            'warehouse_id' => [
                'required',
            ],
            'inventory_transaction_id' => [
                'required',
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'type' => [
                'required',
            ],
        ];
    }
}