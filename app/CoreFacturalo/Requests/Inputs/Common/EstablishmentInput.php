<?php

namespace App\CoreFacturalo\Requests\Inputs\Common;

use App\Models\Tenant\Establishment as EstablishmentModel;

class EstablishmentInput
{
    public static function set($establishment_id)
    {
        $establishment = EstablishmentModel::find($establishment_id);

        return [
            'country_id' => $establishment->country_id,
            'country' => [
                'id' => $establishment->country_id,
                'description' => $establishment->country->description,
            ],
            'department_id' => $establishment->department_id,
            'department' => [
                'id' => $establishment->department_id,
                'description' => $establishment->department->description,
            ],
            'province_id' => $establishment->province_id,
            'province' => [
                'id' => $establishment->province_id,
                'description' => $establishment->province->description,
            ],
            'district_id' => $establishment->district_id,
            'district' => [
                'id' => $establishment->district_id,
                'description' => $establishment->district->description,
            ],
            'urbanization' => $establishment->urbanization,
            'address' => $establishment->address,
            'email' => $establishment->email,
            'telephone' => $establishment->telephone,
            'code' => $establishment->code,
            'trade_address' => $establishment->trade_address,
            'web_address' => $establishment->web_address,
            'aditional_information' => $establishment->aditional_information,
            'logo' => $establishment->logo,
        ];
    }
}
