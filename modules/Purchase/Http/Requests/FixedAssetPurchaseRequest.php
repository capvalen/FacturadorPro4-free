<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FixedAssetPurchaseRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'supplier_id' => [
                'required',
            ],
            'number' => [
                'required',
                'numeric'
            ],
            'series' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ],
        ];
    }
}
