<?php

namespace Modules\Item\Imports;

use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Tenant\ItemSet;
use App\Models\Tenant\Item;


class ItemSetIndividualImport implements ToCollection
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
            $internal_id_item_set = $row[0];
            $internal_id_item_set_individual = $row[1];
            $quantity = $row[2];

            $item_set = Item::whereIsSet()->where('internal_id', $internal_id_item_set)->first();
            $record_item_set_individual = Item::whereNotIsSet()->where('internal_id', $internal_id_item_set_individual)->first();

            if($item_set && $record_item_set_individual) {

                $item_set_individual = ItemSet::where('item_id', $item_set->id)->where('individual_item_id', $record_item_set_individual->id)->first();

                if(!$item_set_individual){

                    $item_set->sets()->create([
                        'individual_item_id' => $record_item_set_individual->id,
                        'quantity' => $quantity,
                    ]);

                    $registered += 1;

                }

            }

        }

        $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }

}
