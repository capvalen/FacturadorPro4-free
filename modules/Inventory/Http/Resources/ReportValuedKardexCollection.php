<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Helpers\InventoryValuedKardex;

class ReportValuedKardexCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        
        return $this->collection->transform(function($row, $key){
            
            $values_records = InventoryValuedKardex::getValuesRecords($row->document_items, $row->sale_note_items);

            $quantity_sale = $values_records['quantity_sale'];
            $total_sales = $values_records['total_sales'];

            $item_cost = $quantity_sale * $row->purchase_unit_price;
            $valued_unit = $total_sales - $item_cost;

            return [

                'id' => $row->id,
                'item_description' => $row->description,
                'category_description' => optional($row->category)->name,
                'brand_description' => optional($row->brand)->name,
                'unit_type_id' => $row->unit_type_id,
                'quantity_sale' => number_format($quantity_sale,2, ".", ""),
                'purchase_unit_price' => number_format($row->purchase_unit_price,2, ".", ""),
                'total_sales' => number_format($total_sales,2, ".", ""),
                'item_cost' => number_format($item_cost,2, ".", ""),
                'valued_unit' => number_format($valued_unit,2, ".", ""),
                'warehouses' => $row->warehouses->transform(function($row, $key){
                    return [
                        'id' => $row->id,
                        'stock' => $row->stock,
                        'warehouse_description' => $row->warehouse->description,
                        'description' => "{$row->warehouse->description} - {$row->stock}",
                    ];
                }),

            ];
        });
    }


}
