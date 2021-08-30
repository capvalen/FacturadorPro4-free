<?php

namespace App\Models\Tenant;
 

class BillingCycle extends ModelTenant
{
    
    protected $fillable = [
        'date_time_start',
        'renew',
        'quantity_documents', 
    ];
    
 
}