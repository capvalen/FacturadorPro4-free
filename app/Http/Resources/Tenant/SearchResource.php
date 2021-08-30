<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer->number,
            'number' => $this->series.'-'.$this->number,
            'total' => (float) $this->total,
            'download_xml' => $this->download_external_xml,
            'download_pdf' => $this->download_external_pdf
        ];
    }
}