<?php

namespace Modules\Order\Models;

use App\Models\Tenant\ModelTenant;

class OrderFormItem extends ModelTenant
{
    public $timestamps = false;

    protected $fillable = [
        'order_form_id',
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
}