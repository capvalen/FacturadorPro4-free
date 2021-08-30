<?php

namespace App\Services;
use App\Models\Tenant\ItemSet;

class ItemSetService
{
    public function getItemsSet($item)
    {
        $records = ItemSet::with('individual_item')->where('item_id', $item)->get();
        $result = array();

        foreach ($records as $row) {

            if(((int)$row->quantity != $row->quantity)){
                $quantity = $row->quantity;
            }
            else{
                $quantity = number_format($row->quantity, 0);
            }

            array_push($result, "{$quantity} - {$row->individual_item->description}");
        }


        return $result;
    }


}
