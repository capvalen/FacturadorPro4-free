<?php
namespace App\Models\Tenant\Catalogs;

use App\Models\Tenant\ModelTenant;

class ModelCatalog extends ModelTenant
{
    public function scopeWhereActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrderByDescription($query)
    {
        return $query->orderBy('description');
    }
}