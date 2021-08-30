<?php

namespace Modules\Expense\Models;
use App\Models\Tenant\ModelTenant;

class ExpenseReason extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'description',
    ];
}