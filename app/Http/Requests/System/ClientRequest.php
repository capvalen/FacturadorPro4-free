<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'email' => [
                'required',
                'email',
            ],
            'number' => [
                'required',
                 Rule::unique('system.clients')->ignore($id),
            ],
            'name' => [
                'required',
                Rule::unique('system.clients')->ignore($id)
            ],
            'password' => [
                'required',
            ],
            'subdomain' => [
                'required'
            ],
            'plan_id' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'soap_send_id' => [
                'required',
            ],
            'soap_type_id' => [
                'required',
            ],
            'soap_username' => [
                'required_if:soap_type_id,"02"'
            ],
            'soap_password' => [
                'required_if:soap_type_id,"02"'
            ],
            'soap_url' => [
                'required_if:soap_send_id,"02"'
            ],


        ];
    }
}
