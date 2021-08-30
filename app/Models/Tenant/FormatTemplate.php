<?php

namespace App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class FormatTemplate extends ModelTenant
{
    use UsesTenantConnection;

    protected $fillable = [
    	'id',
    	'formats'
    ];
}
