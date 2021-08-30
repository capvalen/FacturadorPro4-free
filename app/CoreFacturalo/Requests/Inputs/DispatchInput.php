<?php

namespace App\CoreFacturalo\Requests\Inputs;

use App\CoreFacturalo\Requests\Inputs\Common\ActionInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Requests\Inputs\Common\LegendInput;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\Item;
use Illuminate\Support\Str;

class DispatchInput
{
    public static function set($inputs)
    {
        $document_type_id = $inputs['document_type_id'];
        $series = $inputs['series'];
        $number = $inputs['number'];

        $company = Company::active();
        $soap_type_id = $company->soap_type_id;
        $number = Functions::newNumber($soap_type_id, $document_type_id, $series, $number, Dispatch::class);

        Functions::validateUniqueDocument($soap_type_id, $document_type_id, $series, $number, Dispatch::class);

        $filename = Functions::filename($company, $document_type_id, $series, $number);
        $establishment = EstablishmentInput::set($inputs['establishment_id']);
        $customer = PersonInput::set($inputs['customer_id']);
        $inputs['type'] = 'dispatch';

        return [
            'type' => $inputs['type'],
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => $inputs['establishment_id'],
            'establishment' => $establishment,
            'soap_type_id' => $soap_type_id,
            'state_type_id' => '01',
            'ubl_version' => '2.0',
            'filename' => $filename,
            'document_type_id' => $document_type_id,
            'series' => $series,
            'number' => $number,
            'date_of_issue' => $inputs['date_of_issue'],
            'time_of_issue' => $inputs['time_of_issue'],
            'customer_id' => $inputs['customer_id'],
            'customer' => $customer,
            'observations' => $inputs['observations'],
            'transport_mode_type_id' => $inputs['transport_mode_type_id'],
            'transfer_reason_type_id' => $inputs['transfer_reason_type_id'],
            'transfer_reason_description' => $inputs['transfer_reason_description'],
            'date_of_shipping' => $inputs['date_of_shipping'],
            'transshipment_indicator' => $inputs['transshipment_indicator'],
            'port_code' => $inputs['port_code'],
            'unit_type_id' => $inputs['unit_type_id'],
            'total_weight' => $inputs['total_weight'],
            'packages_number' => $inputs['packages_number'],
            'container_number' => $inputs['container_number'],
            'license_plate' => ( isset($inputs['license_plate']) ) ? $inputs['license_plate'] : null ,
            'origin' => self::origin($inputs),
            'delivery' => self::delivery($inputs),
            'dispatcher' => self::dispatcher($inputs),
            'driver' => self::driver($inputs),
            'items' => self::items($inputs),
            'legends' => LegendInput::set($inputs),
            'optional' => Functions::valueKeyInArray($inputs, 'optional'),
            'actions' => ActionInput::set($inputs),
            'reference_document_id' => Functions::valueKeyInArray($inputs, 'reference_document_id'),
            'reference_quotation_id' => Functions::valueKeyInArray($inputs, 'reference_quotation_id'),
            'reference_order_note_id' => Functions::valueKeyInArray($inputs, 'reference_order_note_id'),
            'reference_order_form_id' => Functions::valueKeyInArray($inputs, 'reference_order_form_id'),
            'reference_sale_note_id' => Functions::valueKeyInArray($inputs, 'reference_sale_note_id'),
            'secondary_license_plates' => self::secondary_license_plates($inputs),
        ];
    }

    private static function origin($inputs)
    {
        if(array_key_exists('origin', $inputs)) {
            $origin = $inputs['origin'];
            $location_id = $origin['location_id'][2] == '0' ? $origin['location_id'] : $origin['location_id'][2];
            $address = $origin['address'];

            return [
                'location_id' => $location_id,
                'address' => $address,
            ];
        }
        return null;
    }

    private static function delivery($inputs)
    {
        if(array_key_exists('delivery', $inputs)) {
            $delivery = $inputs['delivery'];
            $location_id = $delivery['location_id'][2] == '0' ? $delivery['location_id'] : $delivery['location_id'][2];
            $address = $delivery['address'];

            return [
                'location_id' => $location_id,
                'address' => $address,
            ];
        }
        return null;
    }

    private static function dispatcher($inputs)
    {
        if(array_key_exists('dispatcher', $inputs)) {
            $dispatcher = $inputs['dispatcher'];
            $identity_document_type_id = $dispatcher['identity_document_type_id'];
            $number = ( isset($dispatcher['number']) ) ? $dispatcher['number'] : null ; // $dispatcher['number'];
            $name = ( isset($dispatcher['name']) ) ? $dispatcher['name'] : null ; //$dispatcher['name'];

            return  [
                'identity_document_type_id' => $identity_document_type_id,
                'number' => $number,
                'name' => $name,
            ];
        }
        return null;
    }

    private static function driver($inputs)
    {
        if(array_key_exists('driver', $inputs)) {
            $driver = $inputs['driver'];
            $identity_document_type_id = $driver['identity_document_type_id'];
            $number =  ( isset($driver['number']) ) ? $driver['number'] : null ; //$driver['number'];
            $license =  (isset($driver['license'])) ? $driver['license'] : null ;

            return [
                'identity_document_type_id' => $identity_document_type_id,
                'number' => $number,
                'license' => $license,
            ];
        }
        return null;
    }

    private static function items($inputs)
    {
        if(array_key_exists('items', $inputs)) {
            $items = [];
            foreach ($inputs['items'] as $row) {
                $item = Item::find($row['item_id']);
                $items[] = [
                    'item_id' => $item->id,
                    'item' => [
                        'description' => $item->description,
                        'model' => $item->model,
                        'item_type_id' => $item->item_type_id,
                        'internal_id' => $item->internal_id,
                        'item_code' => $item->item_code,
                        'item_code_gs1' => $item->item_code_gs1,
                        'unit_type_id' => $item->unit_type_id,
                        'IdLoteSelected' =>  $row['IdLoteSelected'] ?? null,
                        'lot_group' => $row['lot_group'] ?? null,
                    ],
                    'quantity' => $row['quantity'],
                ];
            }

            return $items;
        }
        return null;
    }

    private static function secondary_license_plates($inputs)
    {

        if(array_key_exists('secondary_license_plates', $inputs)) {

            $secondary_license_plates = $inputs['secondary_license_plates'];
            $semitrailer = $secondary_license_plates['semitrailer'];

            return [
                'semitrailer' => $semitrailer,
            ];

        }

        return null;
    }

}
