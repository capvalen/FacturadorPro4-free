<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class RelatedDocumentType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_related_document_types";
    public $incrementing = false;
}