<?php

namespace App\Models\Tenant; 

class Kardex extends ModelTenant
{
    protected $table = 'kardex';

    protected $fillable = [
        'type',
        'date_of_issue',
        'item_id',
        'document_id',
        'purchase_id',
        'sale_note_id',
        'quantity', 
    ];

    protected $casts = [
        'date_of_issue' => 'date',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}