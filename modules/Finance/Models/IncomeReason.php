<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\ModelTenant;

class IncomeReason extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'description',
    ];
}