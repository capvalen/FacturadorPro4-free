<?php

namespace Modules\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechnicalServiceRequest extends FormRequest
{
     
    public function authorize()
    {
        return true; 
    }
 
    public function rules()
    { 
        
        return [
            'customer_id' => [
                'required',
            ],
            'cellphone' => [
                'required',
                'numeric'
            ],
            'description' => [
                'required',
            ],
            'state' => [
                'required',
            ],
            'date_of_issue' => [
                'required',
            ], 
            'reason' => [
                'required',
            ], 
            'serial_number' => [
                'required',
            ], 
            'cost' => [
                'required',
                'gte:0'
            ], 
            'prepayment' => [
                'required',
                'gte:0'
            ], 
        ];
    }


    public function messages()
    {
        return [
            'cost.gte' => 'El costo debe ser mayor o igual que 0.',
            'prepayment.gte' => 'El pago adelantado debe ser mayor o igual que 0.',
        ];
    }

}
