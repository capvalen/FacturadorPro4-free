<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispatchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        //$id = $this->input('id');

        return [
            'unit_type_id' => [
                'required',
            ],
            // 'transfer_reason_description' => [
            //     'required',
            // ],
            // 'observations' => [
            //     'required',
            // ],
            'delivery.address'=> [
                'required',
                'max:100',
               
            ],
            'dispatcher.identity_document_type_id'=> [
                'required',
            ],
            'dispatcher.number'=> [
                'required',
            ],
            'dispatcher.name'=> [
                'required',
            ],
            'driver.identity_document_type_id'=> [
                'required',
            ],
            'driver.number'=> [
                'required',
            ],
            'license_plate'=> [
                'required',
            ],
            'license_plate'=> [
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
                'max:100',
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
        'license_plate.required' => 'El campo Número de placa del vehiculo es obligatorio.',
        ];
    }
}