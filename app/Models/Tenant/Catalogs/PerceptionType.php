<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class PerceptionType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_perception_types";
    public $incrementing = false;
}