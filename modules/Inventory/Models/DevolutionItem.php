<?php

namespace Modules\Inventory\Models;

use App\Models\Tenant\ModelTenant;

class DevolutionItem extends ModelTenant
{

    public $timestamps = false;

    protected $fillable = [
        'devolution_id',
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

    public function devolution()
    {
        return $this->belongsTo(Devolution::class);
    } 

}