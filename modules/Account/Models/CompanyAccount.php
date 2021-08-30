<?php

namespace Modules\Account\Models;
 
use App\Models\Tenant\ModelTenant;

class CompanyAccount extends ModelTenant
{

    public $timestamps = false;
    
    protected $fillable = [
        'subtotal_pen',
        'total_pen', 
        'igv_pen', 
        'subtotal_usd', 
        'total_usd', 
        'igv_usd', 
    ];
  

}