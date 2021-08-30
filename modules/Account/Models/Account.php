<?php

namespace Modules\Account\Models;
 
use App\Models\Tenant\ModelTenant;

class Account extends ModelTenant
{
    protected $fillable = [
        'number',
        'description', 
    ];
  

}