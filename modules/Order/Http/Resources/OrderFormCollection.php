<?php
namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderFormCollection extends ResourceCollection
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

            $btn_dispatch = $row->dispatch ? false : true;

            
            return [
                'id' => $row->id,
                'btn_dispatch' => $btn_dispatch,
                'external_id' => $row->external_id,
                'soap_type_id' => $row->soap_type_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'number' => $row->number_full,
                'driver_name' => $row->driver->name,
                'customer_name' => $row->customer->name,
                'customer_number' => $row->customer->identity_document_type->description.' '.$row->customer->number,
                'date_of_shipping' => $row->date_of_shipping->format('Y-m-d'),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
