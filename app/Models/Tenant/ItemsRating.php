<?php

namespace App\Models\Tenant;

class ItemsRating extends ModelTenant
{
    protected $table = 'items_rating';

    protected $fillable = [
        'user_id',
        'item_id',
        'item_id',
        'value'
    ];

}