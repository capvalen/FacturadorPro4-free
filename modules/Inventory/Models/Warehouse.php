<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\Establishment;
use App\Models\Tenant\ModelTenant;

class Warehouse extends ModelTenant
{
    protected $fillable = [
        'establishment_id',
        'description',
    ];

    public function inventory_kardex()
    {
        return $this->hasMany(InventoryKardex::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}