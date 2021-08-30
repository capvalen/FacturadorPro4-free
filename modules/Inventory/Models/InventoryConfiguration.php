<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\ModelTenant;

class InventoryConfiguration extends ModelTenant
{

    protected $fillable = [ 
        'stock_control', 
    ];
  
}