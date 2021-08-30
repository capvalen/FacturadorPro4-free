<?php

namespace App\Models\Tenant;
use App\Models\Tenant\Catalogs\Tag;



class ItemTag extends ModelTenant
{ 
    protected $table = 'item_tags';

    protected $with = ['tag'];

    protected $fillable = [
        'item_id',
        'tag_id', 
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

   
}