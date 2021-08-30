<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
     

    public function toArray($request) {
        

        return $this->collection->transform(function($row, $key){ 
             
               
            return [
                'id' => $row->id,
                'date_of_issue' => $row->document->date_of_issue->format('Y-m-d'),
                'customer_name' => $row->document->customer->name,
                'customer_number' => $row->document->customer->number,
                'series' => $row->document->series,
                'alone_number' => $row->document->number,
                'quantity' => number_format($row->quantity,2),
                'total' =>  (in_array($row->document->document_type_id,['01','03']) && in_array($row->document->state_type_id,['09','11'])) ? number_format(0,2) : number_format($row->total,2),
                'document_type_description' => $row->document->document_type->description,
                'document_type_id' => $row->document->document_type->id,   
                'web_platform_name' => optional($row->relation_item->web_platform)->name,   
 

            ];
        });
    }
}
