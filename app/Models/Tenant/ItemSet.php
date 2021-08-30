<?php

namespace App\Models\Tenant;


class ItemSet extends ModelTenant
{

    protected $fillable = [
        'item_id',
        'individual_item_id',    
        'quantity',    
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function individual_item()
    {
        return $this->belongsTo(Item::class, 'individual_item_id');
    }
 
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'individual_item_id');
    }

}