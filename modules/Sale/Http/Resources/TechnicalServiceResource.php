<?php

namespace Modules\Sale\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TechnicalServiceResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'cellphone' => $this->cellphone,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'time_of_issue' => $this->time_of_issue,
            'description' => $this->description,
            'state' => $this->state,
            'reason' => $this->reason,
            'serial_number' => $this->serial_number,
            'cost' => $this->cost,
            'prepayment' => $this->prepayment,
            'activities' => $this->activities,
            'brand' => $this->brand,
            'equipment' => $this->equipment,
            'important_note' => $this->important_note ? $this->important_note : [],
            'repair' => (bool)$this->repair,
            'warranty' => (bool)$this->warranty,
            'maintenance' => (bool)$this->maintenance,
            'diagnosis' => (bool)$this->diagnosis,
        ];
    }
}
