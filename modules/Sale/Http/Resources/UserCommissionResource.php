<?php

namespace Modules\Sale\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCommissionResource extends JsonResource
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
            'user_id' => $this->user_id,  
            'amount' => $this->amount,
            'type' => $this->type,
        ];
    }
}
