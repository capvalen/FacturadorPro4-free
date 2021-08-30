<?php

namespace Modules\Expense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpensePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        return [
            'date_of_payment' => [
                'date',
                'required',
            ],
            'expense_method_type_id' => [
                'required',
            ],
            'payment_destination_id' => [
                'required_unless:expense_method_type_id, "1"',
                // 'required',
            ],
            'payment' => [
                'required',
            ],
        ];
    }
}