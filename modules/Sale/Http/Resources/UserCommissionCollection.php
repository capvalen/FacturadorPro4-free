<?php

namespace Modules\Sale\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCommissionCollection extends ResourceCollection
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
                'user_name' => $row->user->name, 
                'type' => ($row->type == 'amount') ? 'Monto':'Porcentaje',
                'amount' => $row->amount,
            ];
            
        });
    }
    
}
