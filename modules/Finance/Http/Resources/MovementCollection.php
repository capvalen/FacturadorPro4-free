<?php

namespace Modules\Finance\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Finance\Models\GlobalPayment;
use Modules\Finance\Http\Controllers\MovementController;


class MovementCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected static $balance = 0;
    protected static $residuary = 0;
    protected static $request;


    public function toArray($request)
    {

        self::$request = $request;
        $this->calculateResiduary(self::$request);


        return $this->collection->transform(function($row, $key) {
            $data_person = $row->data_person;

            $amount = $row->payment->payment;
            $document = $row->payment->document;
            // Convirtiendo el documento que esta hecho en dolares a soles
            if ($document) {
                if ($document->currency_type_id === 'USD') {
                    $amount = $amount * $document->exchange_rate_sale;
                }
            }
            self::$balance =  ($row->type_movement == 'input') ?  self::$balance + $amount : self::$balance - $amount;

            return [
                'id' => $row->id,
                'destination_description' => $row->destination_description,
                'date_of_payment' => $row->payment->date_of_payment->format('Y-m-d'),
                'payment_method_type_description' => $this->getPaymentMethodTypeDescription($row),
                'reference' => $row->payment->reference,
                'total' => $amount,
                'number_full' => $row->payment->associated_record_payment->number_full,
                'currency_type_id' => $row->payment->associated_record_payment->currency_type_id,
                // 'document_type_description' => ($row->payment->associated_record_payment->document_type) ? $row->payment->associated_record_payment->document_type->description:'NV',
                'document_type_description' => $this->getDocumentTypeDescription($row),
                'person_name' => $data_person->name,
                'person_number' => $data_person->number,
                // 'payment' => $row->payment,
                // 'payment_type' => $row->payment_type,
                'instance_type' => $row->instance_type,
                'instance_type_description' => $row->instance_type_description,
                'user_id' => $row->user_id,
                'user_name' => optional($row->user)->name,

                'type_movement' => $row->type_movement,
                'input' => ($row->type_movement == 'input') ? number_format($amount, 2, ".", "") : '-',
                'output' => ($row->type_movement == 'output') ? number_format($amount, 2, ".", "") : '-',
                'balance' => number_format(self::$balance, 2, ".", ""),
                'items' => $this->getItems($row),


            ];
        });

    }

    public function getItems($row){

        if(in_array($row->instance_type, ['expense', 'income'])){

            return $row->payment->associated_record_payment->items->transform(function($row, $key) {
                return [
                    'description' => $row->description
                ];
            });
        }

        return [];

    }


    public function calculateResiduary($request)
    {

        if($request->page >= 2) {

            $data = app(MovementController::class)->getRecords($request, GlobalPayment::class)->limit(($request->page*20)-20)->get();

            $input = $data->where('type_movement', 'input')->sum('payment.payment');
            $output = $data->where('type_movement', 'output')->sum('payment.payment');

            self::$residuary += $input - $output;
            self::$balance = self::$residuary;

        }

    }


    public function getDocumentTypeDescription($row){

        $document_type = '';

        if($row->payment->associated_record_payment->document_type){

            $document_type = $row->payment->associated_record_payment->document_type->description;

        }elseif(isset($row->payment->associated_record_payment->prefix)){

            $document_type = $row->payment->associated_record_payment->prefix;

        }
        return $document_type;

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
