<?php

namespace Modules\Document\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class ValidateDocumentsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request) {
        return $this->collection->transform(function($row, $key) {
            
            return [
                'id' => $row->id,
                'soap_type_id' => $row->soap_type_id,
                'soap_type_description' => $row->soap_type->description,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'number' => $row->number_full,
                'customer_name' => $row->customer->name,
                'customer_number' => $row->customer->number, 
                'total' => $row->total,
                'state_type_id' => $row->state_type_id,
                'state_type_description' => mb_strtoupper($row->state_type->description),
                'document_type_description' => $row->document_type->description,
                'document_type_id' => $row->document_type->id,  
                'message' => $row->message,
                'code' => $row->code,
                'state_type_sunat_description' => $row->state_type_sunat_description,
            ];
        });
    }
}
