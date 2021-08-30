<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Inventory\Models\InventoryTransaction;

class ReportKardexLotsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        $now = date('Y-m-d');
        return $this->collection->transform(function($row, $key) use ($now) {
            return [
                'id' => $row->id,
                'description' => $row->item->description,
                'stock' => $row->stock,
                'warehouse' => $row->warehouse->description,
                'internal_id' => $row->internal_id,
                'und' => $row->unit_type_id,
                'lot_code' => $row->lot_code,
                'date_of_due' => $row->date_of_due,
                'brand' => $row->brand->name,
                'category' => $row->category->name,
                'days_to_expire' =>1

            ];
        });
    }




}
