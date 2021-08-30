<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;

class PerceptionDocument extends ModelTenant
{
    public $timestamps = false;
    // protected $with = ['document_type', 'currency_type'];

    protected $fillable = [
        'perception_id',
        'document_type_id',
        'series',
        'number',
        'date_of_issue',
        'currency_type_id',
        'total_document',
        'payments',
        'exchange_rate',
        'date_of_perception',
        'total_perception',
        'total_to_pay',
        'total_payment',
    ];

    protected $casts = [
        'date_of_issue' => 'date',
        'date_of_perception' => 'date'
    ];

    public function getPaymentsAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setPaymentsAttribute($value)
    {
        $this->attributes['payments'] = (is_null($value))?null:json_encode($value);
    }

    public function getExchangeRateAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setExchangeRateAttribute($value)
    {
        $this->attributes['exchange_rate'] = (is_null($value))?null:json_encode($value);
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }
}