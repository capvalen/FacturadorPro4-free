<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class TransferReasonType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_transfer_reason_types";
    public $incrementing = false;
}