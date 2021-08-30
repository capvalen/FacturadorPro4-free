<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DevolutionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {
            return [
                'id' => $row->id, 
                'user_id' => $row->user_id, 
                'external_id' => $row->external_id, 
                'user_name' => $row->user->name, 
                'state_type_id' => $row->state_type_id, 
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'), 
                'devolution_reason_id' => $row->devolution_reason_id, 
                'devolution_reason_description' => $row->devolution_reason->description, 
                'observation' => $row->observation, 
                'number_full' => $row->number_full, 
            ];
        });
    }
}