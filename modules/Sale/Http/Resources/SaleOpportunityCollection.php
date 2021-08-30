<?php

namespace Modules\Sale\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleOpportunityCollection extends ResourceCollection
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
        
            $btn_generate = ($row->quotation) ? false : true;
            $btn_generate_oc = ($row->purchase_order) ? false : true;

            return [
                'id' => $row->id, 
                'soap_type_id' => $row->soap_type_id,
                'external_id' => $row->external_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'number_full' => $row->number_full,
                'quotation_number_full' => ($row->quotation) ? $row->quotation->number_full:'',
                'purchase_order_number_full' => ($row->purchase_order) ? $row->purchase_order->number_full:'',
                'user_name' => $row->user->name,
                'customer_name' => $row->customer->name,
                'customer_number' => $row->customer->number,
                'currency_type_id' => $row->currency_type_id,
                'total_exportation' => number_format($row->total_exportation,2),
                'total_free' => number_format($row->total_free,2),
                'total_unaffected' => number_format($row->total_unaffected,2),
                'total_exonerated' => number_format($row->total_exonerated,2),
                'total_taxed' => number_format($row->total_taxed,2),
                'total_igv' => number_format($row->total_igv,2),
                'total' => number_format($row->total,2),
                'state_type_id' => $row->state_type_id, 
                'files' => $row->files, 
                'state_type_description' => $row->state_type->description, 
                'btn_generate' => $btn_generate,
                'btn_generate_oc' => $btn_generate_oc,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
    
}
