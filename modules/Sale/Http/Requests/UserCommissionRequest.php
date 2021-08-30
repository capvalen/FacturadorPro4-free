<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCommissionRequest extends FormRequest
{
     
    public function authorize()
    {
        return true; 
    }
 
    public function rules()
    { 
        $id = $this->input('id');
        
        return [
            'user_id' => [
                'required',
                Rule::unique('tenant.user_commissions')->ignore($id),
            ], 
            'amount' => [
                'required',
                'gt:0'
            ],  
        ];
    }


    public function messages()
    {
        return [
            'amount.gt' => 'El monto debe ser mayor que 0.',
        ];
    }

}
