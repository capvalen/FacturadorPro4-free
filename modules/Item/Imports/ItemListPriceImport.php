<?php

namespace Modules\Item\Imports;

use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Item\Models\Category;
use Modules\Item\Models\Brand;
use App\Models\Tenant\ItemUnitType;


class ItemListPriceImport implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $registered = 0;
            unset($rows[0]);

            foreach ($rows as $row)
            {
                $internal_id = ($row[0])?:null;
                $description = $row[1];
                $unit_type_id = $row[2];
                $factor = $row[3];
                $price1 = $row[4];
                $price2 = $row[5];
                $price3 = $row[6];
                $price_default = $row[7];
 

                $item = null;

                if($internal_id) {
                    $item = Item::where('internal_id', $internal_id)
                                    ->first();
                }

                
                if($item) {
                    
                    $item_unit_type = ItemUnitType::where('item_id', $item->id)
                                                    ->whereDescription($description)
                                                    ->first();


                    if(!$item_unit_type){

                        $item->item_unit_types()->create([
    
                            'description' => $description,
                            'unit_type_id' => $unit_type_id,
                            'quantity_unit' => $factor,
                            'price1' => $price1,
                            'price2' => $price2,
                            'price3' => $price3,
                            'price_default' => $price_default,
    
                        ]);

                    }

                    $registered += 1;

                } 

            }

            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
