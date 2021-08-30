<?php

namespace App\Models\Tenant;

use Hyn\Tenancy\Abstracts\TenantModel;

class ItemWarehousePrice extends TenantModel
{
    protected $table = 'item_warehouse_prices';

    public $timestamps = false;
}
