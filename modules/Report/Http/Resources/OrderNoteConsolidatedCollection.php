<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderNoteConsolidatedCollection extends ResourceCollection
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
                'user' => $row->order_note->user->name,  
                'customer' => $row->order_note->customer->name,  
                'delivery_date' => $row->order_note->delivery_date,  
                'item_description' => $row->item->description,  
                'item_quantity' => $row->quantity,  
                'total' => number_format(self::calculateTotalCurrencyType($row->order_note, $row->total),2, ".", ""),  
            ];
        });
    }

    
    public static function calculateTotalCurrencyType($record, $total)
    {
        return ($record->currency_type_id === 'USD') ? $total * $record->exchange_rate_sale : $total;
    }
    
}
