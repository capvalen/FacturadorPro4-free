<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class SystemIscType extends ModelCatalog
{
    use UsesTenantConnection;
    
    protected $table = "cat_system_isc_types";
    public $incrementing = false;
}