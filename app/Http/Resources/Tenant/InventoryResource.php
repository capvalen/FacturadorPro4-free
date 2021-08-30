<?php

namespace App\Http\Resources\Tenant;

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
            'quantity_move' => 0
        ];
    }
}