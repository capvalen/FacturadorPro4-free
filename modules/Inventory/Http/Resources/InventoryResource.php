<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_description' => $this->item->description,
            'warehouse_id' => $this->warehouse_id,
            'warehouse_description' => $this->warehouse->description,
            'quantity' => $this->stock,
            'warehouse_new_id' => null,
            'quantity_move' => 0,
            'lots_enabled' => (bool)$this->item->lots_enabled,
            'series_enabled' => (bool)$this->item->series_enabled,
            'lots' => $this->item->item_lots->where('has_sale', false)->where('warehouse_id', $this->warehouse_id)->transform(function($row) {
                return [
                    'id' => $row->id,
                    'series' => $row->series,
                    'date' => $row->date,
                    'item_id' => $row->item_id,
                    'warehouse_id' => $row->warehouse_id,
                    'has_sale' => (bool)$row->has_sale,
                    'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
                ];
            })->values(),
        ];
    }
}
