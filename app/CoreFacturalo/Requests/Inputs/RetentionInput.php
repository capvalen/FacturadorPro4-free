<?php

namespace App\CoreFacturalo\Requests\Inputs;

use App\CoreFacturalo\Requests\Inputs\Common\ActionInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Requests\Inputs\Common\ExchangeInput;
use App\CoreFacturalo\Requests\Inputs\Common\LegendInput;
use App\CoreFacturalo\Requests\Inputs\Common\PaymentInput;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Models\Tenant\Company;
use App\Models\Tenant\Retention;
use Illuminate\Support\Str;

class RetentionInput
{
    public static function set($inputs)
    {
        
        $document_type_id = $inputs['document_type_id'];
        $series = $inputs['series'];
        $number = $inputs['number'];

        $company = Company::active();
        $soap_type_id = $company->soap_type_id;
        $number = Functions::newNumber($soap_type_id, $document_type_id, $series, $number, Retention::class);

        Functions::validateUniqueDocument($soap_type_id, $document_type_id, $series, $number, Retention::class);

        $currency_type_id = 'PEN';
        $filename = Functions::filename($company, $document_type_id, $series, $number);
        $establishment = EstablishmentInput::set($inputs['establishment_id']);
        $supplier = PersonInput::set($inputs['supplier_id']);
        $inputs['type'] = 'retention';

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
            'supplier_id' => $inputs['supplier_id'],
            'supplier' => $supplier,
            'retention_type_id' => $inputs['retention_type_id'],
            'observations' => Functions::valueKeyInArray($inputs, 'observations'),
            'currency_type_id' => $currency_type_id,
            'total_retention' => $inputs['total_retention'],
            'total' => $inputs['total'],
            'documents' => self::document($inputs),
            'legends' => LegendInput::set($inputs),
            'actions' => ActionInput::set($inputs),
        ];
    }

    private static function document($inputs)
    {
        if(array_key_exists('documents', $inputs)) {
            $documents = [];
            foreach ($inputs['documents'] as $row)
            {
                $document_type_id = $row['document_type_id'];
                $series = $row['series'];
                $number = $row['number'];
                $date_of_issue = $row['date_of_issue'];
                $currency_type_id = $row['currency_type_id'];
                $total_document = $row['total_document'];
                $date_of_retention = $row['date_of_retention'];
                $total_retention = $row['total_retention'];
                $total_to_pay = $row['total_to_pay'];
                $total_payment = $row['total_payment'];

                $documents[] = [
                    'document_type_id' => $document_type_id,
                    'series' => $series,
                    'number' => $number,
                    'date_of_issue' => $date_of_issue,
                    'currency_type_id' => $currency_type_id,
                    'total_document' => $total_document,
                    'payments' => PaymentInput::set($row),
                    'exchange_rate' => ExchangeInput::set($row),
                    'date_of_retention' => $date_of_retention,
                    'total_retention' => $total_retention,
                    'total_to_pay' => $total_to_pay,
                    'total_payment' => $total_payment,
                ];
            }

            return $documents;
        }
        return null;
    }
}