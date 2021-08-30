<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class SummaryStatusType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_summary_status_types";
    public $incrementing = false;
}