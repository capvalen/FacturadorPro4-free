<?php

namespace Modules\Expense\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Expense\Models\Expense;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        $expense = Expense::with(['items'])->find($this->id);
        $expense->payments = self::getTransformPayments($expense->payments);

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'number' => $this->number ?? '-',
            'state_type_id' => $this->state_type_id,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'payments' => $this->payments->transform(function($row, $key) {
                return [
                    'id' => $row->id,
                    'expense_method_type_description' => $row->expense_method_type->description,
                    'destination_description' => ($row->global_payment) ? $row->global_payment->destination_description:null,
                    'reference' => $row->reference,
                    'payment' => $row->payment,
                ];
            }),
            'expense' => $expense
        ];
    }

    
    public static function getTransformPayments($payments){
        
        return $payments->transform(function($row, $key){ 
            return [
                'id' => $row->id, 
                'expense_id' => $row->expense_id, 
                'date_of_payment' => $row->date_of_payment->format('Y-m-d'), 
                'expense_method_type_id' => $row->expense_method_type_id, 
                'has_card' => $row->has_card, 
                'card_brand_id' => $row->card_brand_id, 
                'reference' => $row->reference, 
                'payment' => $row->payment, 
                'expense_method_type' => $row->expense_method_type, 
                'payment_destination_id' => ($row->global_payment) ? ($row->global_payment->type_record == 'cash' ? null:$row->global_payment->destination_id):null, 
                'payment_filename' => ($row->payment_file) ? $row->payment_file->filename:null, 
                'payment_destination_disabled' => ($row->expense_method_type_id == 1) ? true:false 
            ];
        }); 

    }

}
