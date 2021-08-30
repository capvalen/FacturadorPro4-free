<?php

namespace Modules\Document\Traits;

use App\Models\Tenant\Item;

trait SearchTrait
{

    public function getItemsServices($request)
    {
        $item = Item::whereIsActive()
            ->whereTypeUser();
        if ($request->items_id) {
            return $item->whereIn('id', $request->items_id)
                ->get();
        }
        if ($request->search_by_barcode == 1) {
            return $item->with(['item_lots'])
                ->where('unit_type_id','ZZ')
                ->whereNotIsSet()
                ->where('barcode', $request->input)
                ->limit(1)
                ->get();
        }
        return $item->where('description','like', "%{$request->input}%")
            ->orWhere('internal_id','like', "%{$request->input}%")
            ->orWhereHas('category', function($query) use($request) {
                $query->where('name', 'like', '%' . $request->input . '%');
            })
            ->orWhereHas('brand', function($query) use($request) {
                $query->where('name', 'like', '%' . $request->input . '%');
            })
            ->OrWhereJsonContains('attributes', ['value' => $request->input])
            ->with(['item_lots'])
            ->where('unit_type_id','ZZ')
            ->whereNotIsSet()
            ->orderBy('description')
            ->get();
    }

    public function getItemsNotServices($request)
    {
        $item = Item::whereIsActive()
                ->whereTypeUser();
        if ($request->items_id) {
            return $item
                ->whereIn('id', $request->items_id)
                ->get();
        }
        if ($request->search_by_barcode == 1) {
            return $item
                ->where('barcode', $request->input)
                ->limit(1)
                ->get();
        }
        return $item
            ->with('warehousePrices')
            ->where('description','like', "%{$request->input}%")
            ->orWhere('internal_id','like', "%{$request->input}%")
            ->orWhereHas('category', function($query) use($request) {
                $query->where('name', 'like', '%' . $request->input . '%');
            })
            ->orWhereHas('brand', function($query) use($request) {
                $query->where('name', 'like', '%' . $request->input . '%');
            })
            ->OrWhereJsonContains('attributes', ['value' => $request->input])
            ->whereWarehouse()
            ->orderBy('description')
            ->get();
    }


    public function getItemsServicesById($id){

        return Item::where('id', $id)
                    ->where('unit_type_id','ZZ')
                    ->whereNotIsSet()
                    ->whereIsActive()
                    ->get();

    }

    public function getItemsNotServicesById($id){

        return Item::where('id', $id)
                    ->whereWarehouse()
                    ->whereNotIsSet()
                    ->whereIsActive()
                    ->get();

    }

    public function getFullDescription($row, $warehouse){

        $desc = ($row->internal_id)?$row->internal_id.' - '.$row->description : $row->description;
        $category = ($row->category) ? "{$row->category->name}" : "";
        $brand = ($row->brand) ? "{$row->brand->name}" : "";



        if($row->unit_type_id != 'ZZ')
        {
            $warehouse_stock = 0;
            if($row->warehouses && $warehouse)
            {
                $wr = $row->warehouses->where('warehouse_id', $warehouse->id)->first();
                if($wr)
                {
                    $warehouse_stock = number_format($wr->stock, 2);
                }
            }

            $stock = ($row->warehouses && $warehouse) ? "{$warehouse_stock}" : "";
        }
        else{
            $stock = '';
        }

        $desc = "{$desc} - {$brand}";

        return [
            'full_description' => $desc,
            'warehouse_description' => $warehouse->description,
            'brand' => $brand,
            'category' => $category,
            'stock' => $stock,
        ];
    }

}
