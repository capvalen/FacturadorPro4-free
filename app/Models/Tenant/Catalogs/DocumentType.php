<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class DocumentType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_document_types";
    public $incrementing = false;
}