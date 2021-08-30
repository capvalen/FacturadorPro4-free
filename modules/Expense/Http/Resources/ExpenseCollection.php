<?php

namespace Modules\Expense\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExpenseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {


            return [
                'id' => $row->id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'number' => $row->number,
                'supplier_name' => $row->supplier->name,
                'supplier_number' => $row->supplier->number,
                'currency_type_id' => $row->currency_type_id,
                'state_type_id' => $row->state_type_id,
                'total' => $row->total,
                'expense_type_description' => $row->expense_type->description,
                'expense_reason_description' => $row->expense_reason->description,

                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }

}
