<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        //$id = $this->input('id');

        return [
            'warehouse_destination_id' => [
                'required',
            ],
            'description' => [
                'required',
            ]

        ];
    }


}
