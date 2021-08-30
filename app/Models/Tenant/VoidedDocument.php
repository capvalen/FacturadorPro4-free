<?php

namespace App\Models\Tenant;

class VoidedDocument extends ModelTenant
{
    protected $with = ['document'];
    public $timestamps = false;

    protected $fillable = [
        'voided_id',
        'document_id',
        'description'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}