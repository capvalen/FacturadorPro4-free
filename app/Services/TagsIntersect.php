<?php

namespace App\Services;
use App\Models\Tenant\Item;


class TagsIntersect
{

    public function intersect($array, $item_id)
    {
        $item = Item::find($item_id);
        $array2 = $item->tags->pluck('tag_id')->toArray();
        $result = array_intersect($array, $array2);
        $count = count($result);
        return $count > 0;
    }
    

}