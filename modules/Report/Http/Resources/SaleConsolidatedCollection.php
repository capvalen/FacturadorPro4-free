<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleConsolidatedCollection extends ResourceCollection
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
             
            $unit_type_id = $row->relation_item->unit_type_id;

            if($row->item->presentation){
                $unit_type_id = $row->item->presentation->unit_type_id;
            }

            return [
                'id' => $row->id,
                'item_internal_id' => $row->relation_item->internal_id,  
                'item_unit_type_id' => $unit_type_id,  
                // 'item_unit_type_id' => $row->relation_item->unit_type_id,  
                'item_description' => $row->item->description,  
                'item_quantity' => $row->quantity,  
                'series' => $row->series ?? 'NV',
                'number' => $row->number ?? $row->id,
                'date_of_issue' => $row->date_of_issue,
            ];
        });
    }
    
}
