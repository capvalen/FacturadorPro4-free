<?php

namespace Modules\Padron\Models;
 
use App\Models\Tenant\ModelTenant;

class ChargePadron extends ModelTenant
{
    protected $table = 'charge_padrones';

    protected $fillable = [
       'name', 'reference', 'state'
    ];

}