<?php

namespace App\Models\Tenant;

class SummaryDocument extends ModelTenant
{
    protected $with = ['document'];
    public $timestamps = false;

    protected $fillable = [
        'summary_id',
        'document_id',
        'description'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}