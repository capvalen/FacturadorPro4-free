<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;

class ItemWarehouse extends ModelTenant
{
    protected $table = 'item_warehouse';

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'stock',
    ];

    protected $casts = [
        'stock' => 'float'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function scopeWhereWarehouse($query, $warehouse_id)
    {
        if(!is_null($warehouse_id) && $warehouse_id !== 'all') {
            return $query->where('warehouse_id', $warehouse_id);
        }
        return $query;
    }

//    public function scopeWhereFilter($query, $filter, $stock_min)
//    {
//        if($filter === '02') {
//            return $query->where('stock', '<', 0);
//        }
//        if($filter === '03') {
//            return $query->where('stock', '=', 0);
//        }
//        if($filter === '04') {
//            return $query->where('stock', '>', 0)
//                         ->where('stock', '<=', $stock_min);
//        }
//        if($filter === '05') {
//            return $query->where('stock', '>', $stock_min);
//        }
//
//        return $query;
//    }
}
