<?php

namespace Modules\Finance\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'number' => $this->number,
            'state_type_id' => $this->state_type_id,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'payments' => $this->payments->transform(function($row, $key) {
                return [
                    'id' => $row->id,
                    'payment_method_type_description' => $row->payment_method_type->description,
                    'destination_description' => ($row->global_payment) ? $row->global_payment->destination_description:null,
                    'reference' => $row->reference,
                    'payment' => $row->payment,
                ];
            })
        ];
    }
}
