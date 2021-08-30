<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class PriceType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_price_types";
    public $incrementing = false;
}