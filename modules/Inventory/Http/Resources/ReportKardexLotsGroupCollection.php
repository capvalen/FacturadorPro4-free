<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;


class ReportKardexLotsGroupCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key)  {

            $diff = '';

            if($row->date_of_due)
            {
                $now = Carbon::now();
                $due =   Carbon::parse($row->date_of_due);
                $diff = $now->diffInDays($due);
            }

            return [
                'id' => $row->id,
                'code' => $row->code,
                'quantity' => $row->quantity,
                'date_of_due' => $row->date_of_due,
                'name_item' => $row->item->description,
                'und_item' => $row->item->unit_type_id,
                'code_item' => $row->item->internal_id,
                'diff_days' => $diff,
            ];
        });
    }




}
