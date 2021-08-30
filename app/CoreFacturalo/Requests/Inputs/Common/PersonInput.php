<?php

namespace App\CoreFacturalo\Requests\Inputs\Common;

use App\Models\Tenant\Person as PersonModel;
use App\Models\Tenant\PersonAddress;
use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;


class PersonInput
{
    public static function set($person_id, $address_id = null)
    {
        $person = PersonModel::find($person_id);

        if(!$person) {
            return null;
        }

        $customer_address = null;
        if($address_id)
        {
            $customer_address = PersonAddress::find($address_id);
        }

        return [
            'identity_document_type_id' => $person->identity_document_type_id,
            'identity_document_type' => [
                'id' => $person->identity_document_type_id,
                'description' => $person->identity_document_type->description,
            ],
            'number' => $person->number,
            'name' => $person->name,
            'trade_name' => $person->trade_name,
            'country_id' => $person->country_id,
            'country' => [
                'id' => ($customer_address) ? $customer_address->country_id : $person->country_id,
                'description' => ($customer_address) ?  optional($customer_address->country)->description :  optional($person->country)->description,
            ],
            'department_id' =>  ($customer_address) ? $customer_address->department_id : $person->department_id,
            'department' => [
                'id' =>  ($customer_address) ? $customer_address->department_id : $person->department_id,  //$person->department_id,
                'description' => ($customer_address) ?  optional($customer_address->department)->description :  optional($person->department)->description, // optional($person->department)->description,
            ],
            'province_id' => ($customer_address) ? $customer_address->province_id : $person->province_id, //$person->province_id,
            'province' => [
                'id' => ($customer_address) ? $customer_address->province_id : $person->province_id, //$person->province_id,
                'description' => ($customer_address) ?  optional($customer_address->province)->description :  optional($person->province)->description, //optional($person->province)->description,
            ],
            'district_id' =>  ($customer_address) ? $customer_address->district_id : $person->district_id, //$person->district_id,
            'district' => [
                'id' => ($customer_address) ? $customer_address->district_id : $person->district_id, //$person->district_id,
                'description' => ($customer_address) ?  optional($customer_address->district)->description :  optional($person->district)->description, //optional($person->district)->description,
            ],
            'address' => ($customer_address) ? $customer_address->address : $person->address,//$person->address,
            'email' =>  ($customer_address) ? $customer_address->email : $person->email,  //$person->email,
            'telephone' => ($customer_address) ? $customer_address->telephone : $person->telephone, //$person->telephone,
            'perception_agent' => $person->perception_agent,
            'address_id' => $address_id,
            'internal_code' => $person->internal_code,
        ];
    }
}
