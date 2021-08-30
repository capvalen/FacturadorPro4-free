<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Inventory\Models\ItemWarehouse;

class TransferResource extends JsonResource
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
            'warehouse_destination_id' => $this->warehouse_destination_id,
            'warehouse_description' => $this->warehouse->description,
            'stock' => ItemWarehouse::where([['item_id', $this->item_id],['warehouse_id', $this->warehouse_id]])->first()->stock,
            'quantity' => $this->quantity,
            'detail' => $this->detail,
        ];
    }
}