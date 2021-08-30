<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        
        $response_message = null;
        $response_type = null;
        $code = null;

        if($this->soap_shipping_response){
            if($this->soap_shipping_response->sent){

                $response_message = $this->soap_shipping_response->description;
                $code =  (int) $this->soap_shipping_response->code;

                if($code === 0) {
                    $response_type = 'success';
                }elseif($code < 2000) {
                    $response_type = 'error';
                }elseif ($code < 4000) {
                    $response_type = 'error';
                } else {
                    $response_type = 'warning';
                }
 
            }

        }

        $has_cdr = false;

        if (in_array($this->state_type_id, ['05', '07'])) {
            $has_cdr = true;
        }

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'number' => $this->number_full,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'customer_email' => $this->customer->email,
            'download_external_pdf' => $this->download_external_pdf,
            'customer_telephone' => optional($this->person)->telephone,
            'response_message' => in_array($this->state_type_id, ['07', '09']) ? ($code ? "{$code} - " : '')."{$response_message}" : $response_message,
            'has_cdr' => $has_cdr,
            'response_type' => $response_type,
            'download_cdr' => $this->download_external_cdr,
            'message_text' => "Su guía {$this->number_full} ha sido generada correctamente, puede revisarla en el siguiente enlace: ".url('')."/downloads/dispatch/pdf/{$this->external_id}".""
        ];
    }
}