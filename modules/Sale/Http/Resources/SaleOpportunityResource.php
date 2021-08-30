<?php

namespace Modules\Sale\Http\Resources;

use Modules\Sale\Models\SaleOpportunity;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleOpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sale_opportunity = SaleOpportunity::find($this->id);

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,  
            'number_full' => $this->number_full,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'), 
            'sale_opportunity' => $sale_opportunity
        ];
    }
}
