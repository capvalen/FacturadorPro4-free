<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class LegendType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_legend_types";
    public $incrementing = false;
}