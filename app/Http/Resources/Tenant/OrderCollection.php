<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
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
                'external_id' => $row->external_id,
                'number_document' => $row->number_document,
                'order_id' => str_pad($row->id, 6, "0", STR_PAD_LEFT),
                'customer' => $row->customer->apellidos_y_nombres_o_razon_social,
                'customer_email' => $row->customer->correo_electronico,
                'customer_telefono' => $row->customer->telefono,
                'customer_direccion' => $row->customer->direccion,
                'items' => $row->items,
                'total' => $row->total,
                'reference_payment' => strtoupper($row->reference_payment),
                'document_external_id' => $row->document_external_id,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'status_order_id' => $row->status_order_id,
                'purchase' => $row->purchase
            ];
        });
    }
}
