<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'name' => [
                'required',
                Rule::unique('tenant.companies')->ignore($id),
            ],
            'trade_name' => [
                'required',
                Rule::unique('tenant.companies')->ignore($id),
            ],
            'number' => [
                'required',
                Rule::unique('tenant.companies')->ignore($id),
            ],
            'soap_type_id' => [
                'nullable'
            ],
            'soap_username' => [
                'required_if:soap_type_id,"02"',
                'required_if:soap_send_id,"02"'
            ],
            'soap_password' => [
                'required_if:soap_type_id,"02"',
                'required_if:soap_send_id,"02"'
            ],
        ];
    }
}