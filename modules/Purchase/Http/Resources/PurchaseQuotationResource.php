<?php

namespace Modules\Purchase\Http\Resources;

use App\Models\Tenant\Quotation;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Purchase\Models\PurchaseQuotation; 

class PurchaseQuotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $purchase_quotation = PurchaseQuotation::with(['items'])->find($this->id);

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,  
            'identifier' => $this->identifier,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'), 
            'purchase_quotation' => $purchase_quotation
        ];
    }
}
