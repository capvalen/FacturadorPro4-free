<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DetractionTypeCollection extends ResourceCollection
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
                'active' => ($row->active)?'Si':'No',
                'percentage' => $row->percentage,
                'operation_type_id' => $row->operation_type_id,
                'description' => $row->description,
            ];
        });
    }
}