<?php

namespace Modules\Document\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class DocumentNotSentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request) {
        return $this->collection->transform(function($row, $key) {
            
            $btn_resend = false; 
            $text_tooltip = '';
            $affected_document = null;
            
            if ($row->group_id === '01') {
                if ($row->state_type_id === '01') {
                    $btn_resend = true;
                }
                
                if ($row->state_type_id === '05') { 
                    $btn_resend = false; 
                } 
            } 

            if ($row->group_id === '02') {
                if ($row->state_type_id === '01') {
                    $text_tooltip = 'Envíe mediante resúmen de boletas';
                }
                
                if ($row->state_type_id === '03') { 
                    $text_tooltip = 'Consulte el ticket del resúmen de boletas'; 
                } 
            }

            $now = Carbon::now();
            $date_document = (new Carbon($row->date_of_issue))->addDay();
            $difference_days = 7 - $date_document->diffInDays($now);
            $days_send = ($difference_days <= 0) ? 'El plazo de envío caducó' : $difference_days;

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
                'state_type_description' => $row->state_type->description,
                'document_type_description' => $row->document_type->description,
                'document_type_id' => $row->document_type->id, 
//                'btn_ticket' => $btn_ticket,
                'btn_resend' => $btn_resend,
                'affected_document' => $affected_document,  
                'user_name' => ($row->user) ? $row->user->name : '',
                'user_email' => ($row->user) ? $row->user->email : '',
                'text_tooltip' => $text_tooltip,
                'expiration_days' => $days_send,
                'is_expiration' => ($difference_days <= 0) ? true:false,
            ];
        });
    }
}
