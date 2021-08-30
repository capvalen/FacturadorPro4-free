<?php

namespace Modules\Dashboard\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DashboardStockCollection extends ResourceCollection
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
                'product' => $row->item->description,
                'stock' => number_format($row->stock,2, ".", ""),
                'warehouse' => $row->warehouse->description,
                'state' => ($row->stock <= 0) ? '01': (($row->stock > 0 && $row->stock <= 20) ? '02':'03'),
            ];
        });
    }
}