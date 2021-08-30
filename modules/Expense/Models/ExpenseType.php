<?php

namespace Modules\Expense\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;

class ExpenseType extends ModelTenant
{
    protected $fillable = [
        'description', 
    ];
 
}