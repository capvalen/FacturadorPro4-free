<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class IdentityDocumentType extends ModelCatalog
{
    use UsesTenantConnection;
    
    protected $table = "cat_identity_document_types";
    public $incrementing = false;
}