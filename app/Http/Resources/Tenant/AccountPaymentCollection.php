<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AccountPaymentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return [
                'id' => $row->id,
                'date_of_payment' => $row->date_of_payment->format('d/m/Y'),
                'date_of_payment_real' => ($row->date_of_payment_real) ? $row->date_of_payment_real->format('d/m/Y') : "",
                'comentario' => $row->reference,
                'payment' => $row->payment, 
                'state' => (bool)$row->state,
                'reference_payment' => $row->reference_payment,
                'state_description' => ($row->state) ? 'Pagado': (($row->date_of_payment >= date('Y-m-d')) ? 'Pendiente':'Vencido'),
            ];
        });
    }
}