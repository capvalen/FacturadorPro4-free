<?php

namespace App\Models\System;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodType extends Model
{
    use UsesSystemConnection;

    public $timestamps = false;

    protected $fillable = [
        'description',
    ];
}