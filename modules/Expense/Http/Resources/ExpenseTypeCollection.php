<?php

namespace Modules\Expense\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseTypeCollection extends ResourceCollection
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
                'description' => $row->description 
            ];
        });
    }
}