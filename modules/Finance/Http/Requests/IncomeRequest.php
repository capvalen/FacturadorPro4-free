<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'customer' => [
                'required',
            ],
            'income_reason_id' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ],
        ];
    }
}
