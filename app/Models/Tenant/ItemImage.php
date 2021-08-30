<?php

namespace App\Models\Tenant;

class ItemImage extends ModelTenant
{

    protected $fillable = [
        'item_id',
        'image',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
