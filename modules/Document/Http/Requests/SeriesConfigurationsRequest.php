<?php

namespace Modules\Document\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeriesConfigurationsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'series_id' => [
                'required',
                // 'unique:tenant.series_configurations'
            ],
            'document_type_id' => [
                'required',
                // 'unique:tenant.series_configurations'
            ],
            'series' => [
                'required',
            ],
            'number' => [
                'required',
                'numeric',
                'integer',
                'min:1'
            ],
        ];
    }
}