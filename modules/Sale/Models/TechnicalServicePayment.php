<?php

namespace Modules\Sale\Models;

use Modules\Finance\Models\GlobalPayment;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\ModelTenant;

class TechnicalServicePayment extends ModelTenant
{
    protected $with = ['payment_method_type'];
    public $timestamps = false;

    protected $fillable = [
        'technical_service_id',
        'date_of_payment',
        'payment_method_type_id',
        'reference',
        'change',
        'payment',
    ];

    protected $casts = [
        'date_of_payment' => 'date',
    ];

    public function payment_method_type()
    {
        return $this->belongsTo(PaymentMethodType::class);
    }

    public function global_payment()
    {
        return $this->morphOne(GlobalPayment::class, 'payment');
    }

    public function associated_record_payment()
    {
        return $this->belongsTo(TechnicalService::class, 'technical_service_id');
    }

    public function technical_service()
    {
        return $this->belongsTo(TechnicalService::class);
    }
}
