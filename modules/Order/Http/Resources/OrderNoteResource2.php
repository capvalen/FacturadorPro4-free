<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderNoteResource2 extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'prefix' => $this->prefix,
            'establishment_id' => $this->establishment_id,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $this->date_of_issue->format('H:i:s'),
            'customer_id' => $this->customer_id,
            'currency_type_id' => $this->currency_type_id,
            'purchase_order' => $this->purchase_order,
            'exchange_rate_sale' => $this->exchange_rate_sale,
            'total_prepayment' => $this->total_prepayment,
            'total_charge' => $this->total_charge,
            'total_discount' => $this->total_discount,
            'total_exportation' => $this->total_exportation,
            'total_free' => $this->total_free,
            'total_taxed' => $this->total_taxed,
            'total_unaffected' => $this->total_unaffected,
            'total_exonerated' => $this->total_exonerated,
            'total_igv' => $this->total_igv,
            'total_base_isc' => $this->total_base_isc,
            'total_isc' => $this->total_isc,
            'total_base_other_taxes' => $this->total_base_other_taxes,
            'total_other_taxes' => $this->total_other_taxes,
            'total_taxes' => $this->total_taxes,
            'total_value' => $this->total_value,
            'total' => $this->total,
            'operation_type_id' => $this->operation_type_id,
            'date_of_due' => $this->date_of_due,
            'items' => $this->items,
            'charges' => $this->charges,
            'discounts' => $this->discounts,
            'attributes' => $this->attributes,
            'guides' => $this->guides,
            'additional_information' => $this->additional_information,
            'actions' => $this->actions
        ];
    }
}
