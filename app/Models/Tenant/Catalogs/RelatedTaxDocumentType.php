<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class RelatedTaxDocumentType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_related_tax_document_types";
    public $incrementing = false;
}