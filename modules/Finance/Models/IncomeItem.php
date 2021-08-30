<?php

namespace Modules\Finance\Models;

use App\Models\Tenant\ModelTenant;

class IncomeItem extends ModelTenant
{

    public $timestamps = false;
    
    protected $fillable = [
        'income_id',
        'description',
        'total', 
    ];

    public function income()
    {
        return $this->belongsTo(Income::class);
    }
 
}