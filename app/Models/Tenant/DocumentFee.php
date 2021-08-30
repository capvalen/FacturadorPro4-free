<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;

class DocumentFee extends ModelTenant
{
    public $timestamps = false;
    protected $table = 'document_fee';

    protected $fillable = [
        'document_id',
        'date',
        'currency_type_id',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }
}
