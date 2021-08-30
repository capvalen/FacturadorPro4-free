<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'type' => $this->type,
            'identity_document_type_id' => $this->identity_document_type_id,
            'number' => $this->number,
            'name' => $this->name,
            'internal_code' => $this->internal_code,
            'trade_name' => $this->trade_name,
            'country_id' => $this->country_id,
            'department_id' => $this->department_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'perception_agent' => (bool) $this->perception_agent,
            'percentage_perception' => $this->percentage_perception,
            'state' => $this->state,
            'condition' => $this->condition,
            'person_type_id' => $this->person_type_id,
            'contact' => $this->contact,
            'comment' => $this->comment,
            'addresses' => collect($this->addresses)->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'trade_name' => $row->trade_name,
                    'country_id' => $row->country_id,
                    'location_id' => !is_null($row->location_id)?$row->location_id:[],
                    'address' => $row->address,
                    'phone' => $row->phone,
                    'email' => $row->email,
                    'main' => (bool)$row->main,
                ];
            }),

            // 'more_address' =>  $this->more_address,
        ];
    }
}
