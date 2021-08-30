<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderNoteGeneralCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return $this->collection->transform(function($row, $key){ 
             
            return [
                'id' => $row->id, 
                'delivery_date' => optional($row->delivery_date)->format('Y-m-d'),  
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),  
                'number_full' => $row->number_full,  
                'user_name' => $row->user->name,  
                'customer_name' => $row->customer->name,  
                'customer_number' => $row->customer->number,  
                'total' => $row->total,  
                'state_description' => ($row->documents->count() > 0) ? 'PROCESADO' : 'PENDIENTE',  
            ];
        });
    }
    
}
