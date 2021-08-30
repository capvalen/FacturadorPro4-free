<?php

namespace Modules\BusinessTurn\Models;
 
use App\Models\Tenant\ModelTenant;

class BusinessTurn extends ModelTenant
{
    protected $fillable = [ 
        'value',
        'name',
        'active', 
    ];
  
  

}