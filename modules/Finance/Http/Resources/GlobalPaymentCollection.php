<?php

namespace Modules\Finance\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GlobalPaymentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
 
            $data_person = $row->data_person;

            $document_type = '';

            if($row->payment->associated_record_payment->document_type){

                $document_type = $row->payment->associated_record_payment->document_type->description;
            
            }elseif($row->instance_type == 'technical_service'){
                
                $document_type = 'ST';
                
            }elseif(isset($row->payment->associated_record_payment->prefix)){
                
                $document_type = $row->payment->associated_record_payment->prefix;

            }


            return [
                'id' => $row->id, 
                'destination_description' => $row->destination_description, 
                'date_of_payment' => $row->payment->date_of_payment->format('Y-m-d'), 
                'payment_method_type_description' => $this->getPaymentMethodTypeDescription($row), 
                'reference' => $row->payment->reference, 
                'total' => $row->payment->payment, 
                'number_full' => $row->payment->associated_record_payment->number_full, 
                'currency_type_id' => $row->payment->associated_record_payment->currency_type_id, 
                // 'document_type_description' => ($row->payment->associated_record_payment->document_type) ? $row->payment->associated_record_payment->document_type->description:'NV',
                'document_type_description' => $document_type,
                'person_name' => $data_person->name, 
                'person_number' => $data_person->number, 
                // 'payment' => $row->payment, 
                // 'payment_type' => $row->payment_type, 
                'instance_type' => $row->instance_type, 
                'instance_type_description' => $row->instance_type_description, 
                'user_id' => $row->user_id, 
                'user_name' => optional($row->user)->name, 
            ];
        });
    }


    public function getPaymentMethodTypeDescription($row){

        $payment_method_type_description = '';

        if($row->payment->payment_method_type){

            $payment_method_type_description = $row->payment->payment_method_type->description;

        }else{
            $payment_method_type_description = $row->payment->expense_method_type->description;
        }

        return $payment_method_type_description;
    }
 

}
