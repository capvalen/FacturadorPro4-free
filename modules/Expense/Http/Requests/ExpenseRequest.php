<?php

namespace Modules\Expense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'expense_reason_id' => [
                'required',
            ],
            'number' => [
                // 'required_if:expense_type_id,"1", "2", "3"',
                'nullable',
                'numeric'
            ], 
            'date_of_issue' => [
                'required',
            ], 
        ];
    }
}
