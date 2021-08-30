<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChargeDiscountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'charge_discount_type_id' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'base' => [
                'required',
            ],
            'description' => [
                'required',
            ],
            'percentage' => [
                'required',
            ],
        ];
    }
}