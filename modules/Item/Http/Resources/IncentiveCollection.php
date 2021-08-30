<?php

namespace Modules\Item\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncentiveCollection extends ResourceCollection
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

            return [
                'id' => $row->id,
                'commission_type' => $row->commission_type ? ($row->commission_type == 'amount' ? 'Monto':'Porcentaje') : ($row->commission_amount ? 'Monto':''),
                'commission_amount' => $row->commission_amount,
                'internal_id' => $row->internal_id,
                'description' => $row->description,
                'full_description' => $row->internal_id ? "{$row->internal_id} - {$row->description}":$row->description,

            ];
        });

    }
    
}
