<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\ModelTenant;

class DevolutionReason extends ModelTenant
{

    protected $fillable = [
        'description', 
    ];
 

}
