<?php

namespace Modules\Item\Models;

use App\Models\Tenant\Item;
use App\Models\Tenant\ModelTenant;


class ItemLotsGroup extends ModelTenant
{
    protected $table = 'item_lots_group';


    protected $fillable = [
        'code',
        'quantity',
        'date_of_due',
        'item_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }



}
