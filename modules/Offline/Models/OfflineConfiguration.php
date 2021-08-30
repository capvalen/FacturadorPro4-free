<?php

namespace Modules\Offline\Models;

use App\Models\Tenant\User;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\CurrencyType;
use App\Models\Tenant\ModelTenant;

class OfflineConfiguration extends ModelTenant
{

    protected $fillable = [
        'is_client',
        'token_server',
        'url_server', 
    ];
 

}