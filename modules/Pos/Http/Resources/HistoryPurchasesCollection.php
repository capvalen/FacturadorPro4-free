<?php

namespace Modules\Pos\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class HistoryPurchasesCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request) {
        return $this->collection->transform(function($row, $key) {
            
            $supplier = json_decode($row->supplier);

            return [
                'id' => $row->id,
                'number_full' => "{$row->series}-{$row->number}",
                'series' => $row->series,
                'number' => $row->number,
                'date_of_issue' => $row->date_of_issue,
                'price' => $row->price, 
                'supplier_name' => $supplier->name, 
                'supplier_number' => $supplier->number, 
            ];
        });
    }
}
