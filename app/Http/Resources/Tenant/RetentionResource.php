<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class RetentionResource extends JsonResource
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

        return [
            'id' => $this->id,
            'number_full' => $this->number_full,
            'state_type_id' => $this->state_type_id,
            'supplier_email' => $this->supplier->email,
            'response_message' => in_array($this->state_type_id, ['07', '09']) ? ($code ? "{$code} - " : '')."{$response_message}" : $response_message,
            'response_type' => $response_type,
            'download_cdr' => $this->download_external_cdr,
        ];
        
    }
}