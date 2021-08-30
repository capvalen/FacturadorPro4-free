<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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

        }else if ($this->regularize_shipping) {
            
            $response_message = "Por regularizar: {$this->response_regularize_shipping->code} - {$this->response_regularize_shipping->description}";
            $code =  (int) $this->response_regularize_shipping->code;
            $response_type = 'error';

        }

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'group_id' => $this->group_id,
            'number' => $this->number_full,
            'regularize_shipping' => (bool) $this->regularize_shipping,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'customer_email' => $this->customer->email,
            'download_pdf' => $this->download_external_pdf,
            'print_ticket' => url('')."/print/document/{$this->external_id}/ticket",
            'print_a4' => url('')."/print/document/{$this->external_id}/a4",
            'print_a5' => url('')."/print/document/{$this->external_id}/a5",
            'image_detraction' => ($this->detraction) ? (($this->detraction->image_pay_constancy) ?
            asset('storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'image_detractions'.DIRECTORY_SEPARATOR.$this->detraction->image_pay_constancy):false):false,
            'detraction' => $this->detraction,
            'response_message' => $response_message,
            'response_type' => $response_type,
            'customer_telephone' => optional($this->person)->telephone,
            'message_text' => "Su comprobante de pago electrónico {$this->number_full} ha sido generado correctamente, puede revisarlo en el siguiente enlace: ".url('')."/print/document/{$this->external_id}/a4".""
        ];
    }
}