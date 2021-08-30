<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\StateType;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VoidedCollection extends ResourceCollection
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

            $btn_ticket = true;
            $has_xml = true;
            $has_pdf = true;
            $has_cdr = false;

            if($row->state_type_id === '05') {
                $btn_ticket = false;
                $has_cdr = true;
            }

            $download_external_xml = route('tenant.download.external_id', ['model' => str_singular($row->type), 'type' => 'xml', 'external_id' => $row->external_id]);
            $download_external_pdf = route('tenant.download.external_id', ['model' => str_singular($row->type), 'type' => 'pdf', 'external_id' => $row->external_id]);
            $download_external_cdr = route('tenant.download.external_id', ['model' => str_singular($row->type), 'type' => 'cdr', 'external_id' => $row->external_id]);

            return [
                'type' => $row->type,
                'id' => $row->id,
                'ticket' => $row->ticket,
                'identifier' => $row->identifier,
                'date_of_issue' => $row->date_of_issue,
                'date_of_reference' => $row->date_of_reference,
                'state_type_id' => $row->state_type_id,
                'state_type_description' => StateType::find($row->state_type_id)->description,
                'has_xml' => $has_xml,
                'has_pdf' => $has_pdf,
                'has_cdr' => $has_cdr,
                'download_xml' => $download_external_xml,
                'download_pdf' => $download_external_pdf,
                'download_cdr' => $download_external_cdr,
                'btn_ticket' => $btn_ticket,
//                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
//                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}