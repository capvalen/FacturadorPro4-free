<?php

namespace App\Models\Tenant;

class PaymentCondition extends ModelTenant
{
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'days',
        'is_locked',
        'is_active',
    ];
}
