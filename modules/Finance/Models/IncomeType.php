<?php

namespace Modules\Finance\Models;
 
use App\Models\Tenant\ModelTenant;

class IncomeType extends ModelTenant
{
    protected $fillable = [
        'description', 
    ];
 
}