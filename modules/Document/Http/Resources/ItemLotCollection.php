<?php

namespace Modules\Document\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemLotCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request) {
        return $this->collection->transform(function($row, $key) {
             
            return [
                'id' => $row->id,
                'series' => $row->series,
                'date' => $row->date,
                'item_id' => $row->item_id,
                'warehouse_id' => $row->warehouse_id,
                'has_sale' => (bool)$row->has_sale,
                'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
            ];
        });
    }
}
