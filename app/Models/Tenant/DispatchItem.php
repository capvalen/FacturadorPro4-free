<?php

namespace App\Models\Tenant;

class DispatchItem extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'dispatch_id',
        'item_id',
        'item',
        'quantity',
    ];

    public function getItemAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setItemAttribute($value)
    {
        $this->attributes['item'] = (is_null($value))?null:json_encode($value);
    }
    
    public function relation_item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    
}