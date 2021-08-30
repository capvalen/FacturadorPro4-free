<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RetentionRequest extends FormRequest
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
            'document_type_id' => [
                'required',
            ],
            // 'series_id' => [
            //     'required',
            // ],
            'supplier_id' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ],
            'observations' => [
                'required',
            ],
        ];
    }
}