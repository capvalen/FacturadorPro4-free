<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfigurationRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        $id = $this->input('id');

        return [
            'send_auto' => ['required', 'boolean'],
            'cron' => ['required', 'boolean'],
            'decimal_quantity' => ['required', 'integer'],


            // 'subtotal_account' => ['required'],
            // 'stock' => ['required', 'boolean']
        ];
    }
}
