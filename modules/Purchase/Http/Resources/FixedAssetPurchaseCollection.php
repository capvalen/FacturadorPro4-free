<?php

namespace Modules\Purchase\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FixedAssetPurchaseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {

            $total = $row->total;
            if($row->total_perception)
            {
                $total += round($row->total_perception, 2);
            }

            return [
                'id' => $row->id,
                'document_type_description' => $row->document_type->description,
                'group_id' => $row->group_id,
                'soap_type_id' => $row->soap_type_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'date_of_due' => ($row->date_of_due) ? $row->date_of_due->format('Y-m-d'):'-',
                'number' => $row->number_full,
                'supplier_name' => $row->supplier->name,
                'supplier_number' => $row->supplier->number,
                'currency_type_id' => $row->currency_type_id,
                'total_exportation' => $row->total_exportation,
                'total_free' => number_format($row->total_free, 2, ".",""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".",""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".",""),
                'total_taxed' => number_format($row->total_taxed, 2, ".",""),
                'total_igv' => number_format($row->total_igv, 2, ".",""),
                'total_perception' => number_format($row->total_perception, 2, ".",""),
                'total' => number_format($total, 2, ".",""),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
                'items' => $row->items->transform(function($row, $key) {
                    return [
                        'key' => $key + 1,
                        'id' => $row->id,
                        'description' => $row->item->description,
                        'quantity' => round($row->quantity,2)
                    ];
                }),
            ];
        });
    }

}
