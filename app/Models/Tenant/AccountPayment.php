<?php

namespace App\Models\Tenant;


class AccountPayment extends ModelTenant
{
    

    protected $fillable = [
        'client_id',
        'date_of_payment',
        'date_of_payment_real',
        'reference_id',
        'payment_method_type_id',
        'has_card',
        'card_brand_id',
        'reference',
        'payment',
        'state',
        'reference_payment'
    ];

    protected $casts = [
        'date_of_payment' => 'date',
        'date_of_payment_real' => 'date'
    ];

    
}