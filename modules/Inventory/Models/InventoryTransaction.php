<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;

class InventoryTransaction extends ModelTenant
{

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'id', 
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

}