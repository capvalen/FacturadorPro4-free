<?php

namespace Modules\Purchase\Http\Resources;

use Modules\Purchase\Models\FixedAssetPurchase;

use Illuminate\Http\Resources\Json\JsonResource;

class FixedAssetPurchaseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $fa_purchase = FixedAssetPurchase::find($this->id);
        
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'group_id' => $this->group_id,
            'number_full' => $this->number_full,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'fa_purchase' => $fa_purchase
             
        ];
    }
 
}
