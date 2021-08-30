<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class ChargeDiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'charge_discount_type_id' => $this->charge_discount_type_id,
            'type' => $this->type,
            'base' => (bool) $this->base,
            'description' => $this->description,
            'percentage' => $this->percentage,
        ];
    }
}