<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        //$id = $this->input('id');

        return [
            'establishment_id' => [
                'required',
            ],
            'unit_type_id' => [
                'required',
            ],
            'transfer_reason_description' => [
                'required',
            ],
            'observations' => [
                'required',
            ],
            'delivery.address'=> [
                'required',
               
            ],

            'customer_id'=> [
                'required',
            ],
            'transport_mode_type_id'=> [
                'required',
            ],
            'transfer_reason_type_id'=> [
                'required',
            ],
            'origin.address'=> [
                'required',
            ],

            'dispatcher_id'=> [
                'required',
            ],
            'driver_id'=> [
                'required',
            ],
            
            'license_plates.license_plate_1'=> [
                'required',
            ],
            'license_plates.register_number_1'=> [
                'required',
            ],
            'license_plates.license_plate_2'=> [
                'required',
            ],
            'license_plates.register_number_2'=> [
                'required',
            ],
            
           
        ];
    }

    public function messages()
    {
        return [
            'transfer_reason_description.required' => 'El campo Descripción de motivo de traslado es obligatorio.',
            'observations.required' => 'El campo Observaciones es obligatorio.',
            'dispatcher.identity_document_type_id.required' => 'El campo Tipo Doc. Identidad es obligatorio.',
            'dispatcher.number.required' => 'El campo Número es obligatorio.',
            'dispatcher.name.required' => 'El campo Nombre y/o razón social es obligatorio.',
            'driver.identity_document_type_id.required' => 'El campo Tipo Doc. Identidad es obligatorio.',
            'driver.number.required' => 'El campo Número es obligatorio.',
        ];
    }
}