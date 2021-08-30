<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;

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
    
}