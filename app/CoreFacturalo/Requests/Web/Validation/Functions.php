<?php

namespace App\CoreFacturalo\Requests\Web\Validation;

use App\Models\Tenant\{
    Establishment,
    Document,
    Series,
    Person,
    Item
};
use Exception;

class Functions
{
    public static function establishment($inputs) {
        $establishment = Establishment::where('code', $inputs['code'])->first();
        
        if ($establishment) {
            return $establishment->id;
        }
        
        throw new Exception("El código ingresado del establecimiento es incorrecto.");
    }
    
    public static function validateSeries($inputs) {
        $series = Series::query()
            ->where('number', $inputs['series'])
            ->where('document_type_id', $inputs['document_type_id'])
            ->where('establishment_id', $inputs['establishment_id'])
            ->first();
            
        if (!$series) {
            throw new Exception("La serie ingresada {$inputs['series']}, es incorrecta.");
        }
    }
    
    public static function person($inputs, $type) {
        if (isset($inputs['id'])) return Person::find($inputs['id'])->id;
        
        $district_id = $inputs['district_id'];
        $province_id = ($district_id) ? substr($district_id, 0 ,4) : null;
        $department_id = ($district_id) ? substr($district_id, 0 ,2) : null;
        
        $person = Person::updateOrCreate( [
                'type' => $type,
                'identity_document_type_id' => $inputs['identity_document_type_id'],
                'number' => $inputs['number'],
            ], [
                'name' => $inputs['name'],
                'trade_name' => $inputs['trade_name'],
                'country_id' => $inputs['country_id'],
                'department_id' => $department_id,
                'province_id' => $province_id,
                'district_id' => $district_id,
                'address' => $inputs['address'],
                'email' => $inputs['email'],
                'telephone' => $inputs['telephone'],
        ]);
        
        return $person->id;
    }
    
    public static function item($inputs) {
        $item = Item::updateOrCreate([
                'internal_id' => $inputs['internal_id'],
            ], [
                'description' => $inputs['description'],
                'item_type_id' => $inputs['item_type_id'],
                'item_code' => $inputs['item_code'],
                'item_code_gs1' => $inputs['item_code_gs1'],
                'unit_type_id' => $inputs['unit_type_id'],
                'currency_type_id' => $inputs['currency_type_id'],
                'unit_price' =>  $inputs['unit_price'],
        ]);
        
        return $item->id;
    }
    
    public static function findAffectedDocumentByExternalId($external_id) {
        $document = Document::where('external_id', $external_id)
            ->first();
            
        if (!$document) {
            throw new Exception("No se encontró el documento con código externo {$external_id}.");
        }
        return $document;
    }
    
    public static function findSeries($inputs)
    {
        return Series::find($inputs['series_id']);
    }
    
    // public static function findSeries($inputs) {
    //     if(!$inputs['series_id']) throw new Exception("La serie no existe");
    //     return Series::find($inputs['series_id']);
    // }
    
    public static function findAffectedDocument($inputs) {
        return Document::find($inputs['affected_document_id']);
    }
    
    public static function DNI($inputs){

        if (($inputs['document_type_id'] == '03') && ($inputs['total']) > 700) {
            $person = Person::query()
                ->with('identity_document_type')
                ->find($inputs['customer_id']);
            
            if (!in_array($person->identity_document_type_id, ['1','6','4','7'], true)) throw new Exception("El tipo doc. identidad {$person->identity_document_type->description} del cliente no es valido.");
        }

    }

    public static function identityDocumentTypeInvoice($inputs){

        $person = Person::find($inputs['customer_id']);

        if($person){

            if (($inputs['operation_type_id'] == '0101')) {
                if (($inputs['document_type_id'] == '01')) { 
                    if (!in_array($person->identity_document_type_id, ['6'], true)) throw new Exception("El tipo doc. identidad {$person->identity_document_type->description} del cliente no es válido.");
                }
            }

            // if ($inputs['document_type_id'] === '01') {
            //     if (!in_array($person->identity_document_type_id, ['6'], true)) {
            //         throw new Exception("El tipo doc. identidad {$person->identity_document_type->description} del cliente no es válido.");
            //     }
            // }

            if ($inputs['document_type_id'] === '03') {
                if ($inputs['total'] >= 700) {
                    if (in_array($person->identity_document_type_id, ['0'], true)) {
                        throw new Exception("El tipo doc. identidad {$person->identity_document_type->description} del cliente no es válido, el monto supera el monto base.");
                    }
                }
            }

        }      

    }
    
}
