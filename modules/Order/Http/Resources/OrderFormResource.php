<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Order\Models\OrderForm;

class OrderFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $order_form = OrderForm::find($this->id);

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'number' => $this->number_full,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'customer_email' => $this->customer->email,
            'customer_telephone' => optional($this->person)->telephone,
            'order_form' => $order_form,
            'message_text' => "Su orden de pedido {$this->number_full} ha sido generada correctamente, puede revisarla en el siguiente enlace: ".url('')."/order-forms/print/{$this->external_id}/a4".""
        ];
    }
}