<?php

namespace Modules\Inventory\Http\Resources;
use Modules\Inventory\Models\Warehouse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransferCollection extends ResourceCollection
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
                'description' => $row->description,
                'quantity' => round($row->quantity, 1),
                'warehouse' => $row->warehouse->description,
                'warehouse_destination' => $row->warehouse_destination->description,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'inventory' => $row->inventory->transform(function($o) use ($row) {
                    return [
                        'id' => $o->item->id,
                        'description' => $o->item->description,
                        'quantity' => $o->quantity,
                        'lots_enabled' => (bool)$o->item->lots_enabled,
                        'lots' => $o->item->item_lots->where('has_sale', false)->where('warehouse_id', $row->warehouse_destination_id)->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'series' => $row->series,
                                'date' => $row->date,
                                'item_id' => $row->item_id,
                                'warehouse_id' => $row->warehouse_id,
                                'has_sale' => (bool)$row->has_sale,
                                'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
                            ];
                        }),
                    ];
                })
            ];
        });
    }
}
