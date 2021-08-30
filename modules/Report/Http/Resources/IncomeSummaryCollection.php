<?php

namespace Modules\Report\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class IncomeSummaryCollection extends ResourceCollection
{
     

    public function toArray($request) {
        

        return $this->collection->transform(function($row, $key){ 
             
 
               
            return [
                'id' => $row->id,
                // 'group_id' => $row->group_id,
                'soap_type_id' => $row->soap_type_id,
                'soap_type_description' => $row->soap_type->description,
                // 'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                // 'number' => $row->number_full,
                // 'customer_name' => $row->customer->name,
                // 'customer_number' => $row->customer->number,
                // 'currency_type_id' => $row->currency_type_id, 
                // 'total_taxed' => $row->total_taxed,
                // 'total_igv' =>  $row->total_igv,
                // 'total' =>  $row->total, 
                // 'state_type_id' => $row->state_type_id,
                // 'state_type_description' => $row->state_type->description,
                // 'document_type_description' => $row->document_type->description,
                // 'document_type_id' => $row->document_type->id,   
                // 'user_name' => ($row->user) ? $row->user->name : '',
                // 'user_email' => ($row->user) ? $row->user->email : '',
 

            ];
        });
    }
}
