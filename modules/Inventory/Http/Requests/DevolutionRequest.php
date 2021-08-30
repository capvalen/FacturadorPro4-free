<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DevolutionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'establishment_id' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ],
            'devolution_reason_id' => [
                'required',
            ], 
            'observation' => [
                'required',
            ],
        ];
    }
}