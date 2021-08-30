<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SummaryCollection extends ResourceCollection
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
            $btn_ticket = false;
            $has_xml = false;
            $has_cdr = false;

            if ($row->state_type_id === '03') {
                $has_xml = true;
                $btn_ticket = true;
            }
            if (in_array($row->state_type_id, ['05', '09'])) {
                $has_xml = true;
                $has_cdr = true;
                $btn_ticket = false;
            }
            return [
                'id' => $row->id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'date_of_reference' => $row->date_of_reference->format('Y-m-d'),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'ticket' => $row->ticket,
                'identifier' => $row->identifier,
                'has_xml' => $has_xml,
                'has_cdr' => $has_cdr,
                'download_xml' => $row->download_external_xml,
                'download_cdr' => $row->download_external_cdr,
                'btn_ticket' => $btn_ticket,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}