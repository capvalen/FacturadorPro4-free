<?php

namespace App\CoreFacturalo\Requests\Inputs;

use App\CoreFacturalo\Requests\Inputs\Common\ActionInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Requests\Inputs\Common\LegendInput;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use App\Models\Tenant\Item;
use Illuminate\Support\Str;
use App\CoreFacturalo\Requests\Inputs\Transform\DocumentWebTransform;
use Modules\Offline\Models\OfflineConfiguration;
use Illuminate\Support\Facades\Storage;

class DocumentInput
{

    public static function set($inputs)
    {
        $document_type_id = $inputs['document_type_id'];
        $series = $inputs['series'];
        $number = $inputs['number'];

        $company = Company::active();
        $soap_type_id = $company->soap_type_id;

        $offline_configuration = OfflineConfiguration::firstOrFail();
        // $number = Functions::newNumber($soap_type_id, $document_type_id, $series, $number, Document::class);

        if ($number !== '#') {
            Functions::validateUniqueDocument($soap_type_id, $document_type_id, $series, $number, Document::class);
        }

        // $filename = Functions::filename($company, $document_type_id, $series, $number);
        $establishment = EstablishmentInput::set($inputs['establishment_id']);
        $customer = PersonInput::set($inputs['customer_id'], isset($inputs['customer_address_id']) ? $inputs['customer_address_id'] : null);

        if (in_array($document_type_id, ['01', '03'])) {
            $array_partial = self::invoice($inputs);
            $invoice = $array_partial['invoice'];
            $note = null;
        } else {
            $array_partial = self::note($inputs);
            $note = $array_partial['note'];
            $invoice = null;
        }

        $inputs['type'] = $array_partial['type'];
        $inputs['group_id'] = $array_partial['group_id'];

        //set o convert json

        if ($offline_configuration->is_client) {
            $exist_data_json = Functions::valueKeyInArray($inputs, 'data_json');
            $data_json = ($exist_data_json) ? $exist_data_json : DocumentWebTransform::transform($inputs);
        } else {
            $data_json = Functions::valueKeyInArray($inputs, 'data_json');
        }

        return [
            'type' => $inputs['type'],
            'group_id' => $inputs['group_id'],
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment_id' => $inputs['establishment_id'],
            'establishment' => $establishment,
            'soap_type_id' => $soap_type_id,
            'state_type_id' => '01',
            'ubl_version' => '2.1',
            'filename' => '',//$filename,
            'document_type_id' => $document_type_id,
            'series' => $series,
            'number' => $number,
            'date_of_issue' => $inputs['date_of_issue'],
            'time_of_issue' => $inputs['time_of_issue'],
            'customer_id' => $inputs['customer_id'],
            'seller_id' => Functions::valueKeyInArray($inputs, 'seller_id'),
            'customer' => $customer,
            'currency_type_id' => $inputs['currency_type_id'],
            'purchase_order' => Functions::valueKeyInArray($inputs, 'purchase_order'),
            'quotation_id' => Functions::valueKeyInArray($inputs, 'quotation_id'),
            'sale_note_id' => Functions::valueKeyInArray($inputs, 'sale_note_id'),
            'order_note_id' => Functions::valueKeyInArray($inputs, 'order_note_id'),
            'exchange_rate_sale' => $inputs['exchange_rate_sale'],
            'total_prepayment' => Functions::valueKeyInArray($inputs, 'total_prepayment', 0),
            'total_discount' => Functions::valueKeyInArray($inputs, 'total_discount', 0),
            'total_charge' => Functions::valueKeyInArray($inputs, 'total_charge', 0),
            'total_exportation' => Functions::valueKeyInArray($inputs, 'total_exportation', 0),
            'total_free' => Functions::valueKeyInArray($inputs, 'total_free', 0),
            'total_taxed' => $inputs['total_taxed'],
            'total_unaffected' => Functions::valueKeyInArray($inputs, 'total_unaffected', 0),
            'total_exonerated' => Functions::valueKeyInArray($inputs, 'total_exonerated', 0),
            'total_igv' => $inputs['total_igv'],
            'total_base_isc' => Functions::valueKeyInArray($inputs, 'total_base_isc', 0),
            'total_isc' => Functions::valueKeyInArray($inputs, 'total_isc', 0),
            'total_base_other_taxes' => Functions::valueKeyInArray($inputs, 'total_base_other_taxes', 0),
            'total_other_taxes' => Functions::valueKeyInArray($inputs, 'total_other_taxes', 0),
            'total_plastic_bag_taxes' => Functions::valueKeyInArray($inputs, 'total_plastic_bag_taxes', 0),
            'total_taxes' => $inputs['total_taxes'],
            'total_value' => $inputs['total_value'],
            'total' => $inputs['total'],
            'has_prepayment' => Functions::valueKeyInArray($inputs, 'has_prepayment', 0),
            'affectation_type_prepayment' => Functions::valueKeyInArray($inputs, 'affectation_type_prepayment'),
            'was_deducted_prepayment' => Functions::valueKeyInArray($inputs, 'was_deducted_prepayment', 0),
            'pending_amount_prepayment' => Functions::valueKeyInArray($inputs, 'pending_amount_prepayment', 0),
            'items' => self::items($inputs),
            'charges' => self::charges($inputs),
            'discounts' => self::discounts($inputs),
            'prepayments' => self::prepayments($inputs),
            'guides' => self::guides($inputs),
            'related' => self::related($inputs),
            'perception' => self::perception($inputs),
            'detraction' => self::detraction($inputs),
            'invoice' => $invoice,
            'note' => $note,
            'hotel' => self::hotel($inputs),
            'transport' => self::transport($inputs),
            'additional_information' => Functions::valueKeyInArray($inputs, 'additional_information'),
            'plate_number' => Functions::valueKeyInArray($inputs, 'plate_number'),
            'legends' => LegendInput::set($inputs),
            'actions' => ActionInput::set($inputs),
            'data_json' => $data_json,
            'payments' => Functions::valueKeyInArray($inputs, 'payments', []),
            'send_server' => false,
            'payment_method_type_id' => Functions::valueKeyInArray($inputs, 'payment_method_type_id'),
            'reference_data' => Functions::valueKeyInArray($inputs, 'reference_data'),
            'terms_condition' => $inputs['terms_condition'] ?? '',
            'dispatches_relateds' => $inputs['dispatches_relateds'] ?? null,
            'sale_notes_relateds' => $inputs['sale_notes_relateds'] ?? null,
            'payment_condition_id' => key_exists('payment_condition_id', $inputs) ? $inputs['payment_condition_id'] : '01',
            'fee' => Functions::valueKeyInArray($inputs, 'fee', []),
            'is_editable' => true,
        ];
    }

    public static function items($inputs)
    {
        if (array_key_exists('items', $inputs)) {
            $items = [];
            foreach ($inputs['items'] as $row) {
                $item = Item::query()->find($row['item_id']);
                $items[] = [
                    'item_id' => $item->id,
                    'item' => [
                        'description' => trim($item->description),
                        'item_type_id' => $item->item_type_id,
                        'internal_id' => $item->internal_id,
                        'item_code' => trim($item->item_code),
                        'item_code_gs1' => $item->item_code_gs1,
                        'unit_type_id' => (key_exists('item', $row)) ? $row['item']['unit_type_id'] : $item->unit_type_id,
                        'presentation' => (key_exists('item', $row)) ? (isset($row['item']['presentation']) ? $row['item']['presentation'] : []) : [],
                        'amount_plastic_bag_taxes' => $item->amount_plastic_bag_taxes,
                        'is_set' => $item->is_set,
                        'lots' => self::lots($row),
                        'IdLoteSelected' => (isset($row['IdLoteSelected']) ? $row['IdLoteSelected'] : null),
                        'model' => $item->model,
                        'date_of_due' => (!empty($item->date_of_due)) ? $item->date_of_due->format('Y-m-d') : null,
                        'has_igv' => $row['item']['has_igv'] ?? true,
                        'unit_price' => $row['unit_price'] ?? 0,
                    ],
                    'quantity' => $row['quantity'],
                    'unit_value' => $row['unit_value'],
                    'price_type_id' => $row['price_type_id'],
                    'unit_price' => $row['unit_price'],
                    'affectation_igv_type_id' => $row['affectation_igv_type_id'],
                    'total_base_igv' => $row['total_base_igv'],
                    'percentage_igv' => $row['percentage_igv'],
                    'total_igv' => $row['total_igv'],
                    'system_isc_type_id' => Functions::valueKeyInArray($row, 'system_isc_type_id'),
                    'total_base_isc' => Functions::valueKeyInArray($row, 'total_base_isc', 0),
                    'percentage_isc' => Functions::valueKeyInArray($row, 'percentage_isc', 0),
                    'total_isc' => Functions::valueKeyInArray($row, 'total_isc', 0),
                    'total_base_other_taxes' => Functions::valueKeyInArray($row, 'total_base_other_taxes', 0),
                    'percentage_other_taxes' => Functions::valueKeyInArray($row, 'percentage_other_taxes', 0),
                    'total_other_taxes' => Functions::valueKeyInArray($row, 'total_other_taxes', 0),
                    'total_plastic_bag_taxes' => Functions::valueKeyInArray($row, 'total_plastic_bag_taxes', 0),
                    'total_taxes' => $row['total_taxes'],
                    'total_value' => $row['total_value'],
                    'total_charge' => Functions::valueKeyInArray($row, 'total_charge', 0),
                    'total_discount' => Functions::valueKeyInArray($row, 'total_discount', 0),
                    'total' => $row['total'],
                    'attributes' => self::attributes($row),
                    'discounts' => self::discounts($row),
                    'charges' => self::charges($row),
                    'warehouse_id' => Functions::valueKeyInArray($row, 'warehouse_id'),
                    'additional_information' => Functions::valueKeyInArray($row, 'additional_information'),
                    'name_product_pdf' => Functions::valueKeyInArray($row, 'name_product_pdf')
                ];
            }
            return $items;
        }
        return null;
    }

    private static function lots($row)
    {
        if (isset($row['item']['lots'])) {
            return $row['item']['lots'];
        } else if (isset($row['lots'])) {
            return $row['lots'];
        } else {
            return [];
        }
    }

    private static function attributes($inputs)
    {
        if (array_key_exists('attributes', $inputs)) {
            if ($inputs['attributes']) {
                $attributes = [];
                foreach ($inputs['attributes'] as $row) {
                    $attribute_type_id = $row['attribute_type_id'];
                    $description = $row['description'];
                    $value = array_key_exists('value', $row) ? $row['value'] : null;
                    $start_date = array_key_exists('start_date', $row) ? $row['start_date'] : null;
                    $end_date = array_key_exists('end_date', $row) ? $row['end_date'] : null;
                    $duration = array_key_exists('duration', $row) ? $row['duration'] : null;

                    $attributes[] = [
                        'attribute_type_id' => $attribute_type_id,
                        'description' => $description,
                        'value' => $value,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'duration' => $duration,
                    ];
                }
                return $attributes;
            }
        }
        return null;
    }

    private static function charges($inputs)
    {
        if (array_key_exists('charges', $inputs)) {
            if ($inputs['charges']) {
                $charges = [];
                foreach ($inputs['charges'] as $row) {
                    $charge_type_id = $row['charge_type_id'];
                    $description = $row['description'];
                    $factor = $row['factor'];
                    $amount = $row['amount'];
                    $base = $row['base'];

                    $charges[] = [
                        'charge_type_id' => $charge_type_id,
                        'description' => $description,
                        'factor' => $factor,
                        'amount' => $amount,
                        'base' => $base,
                    ];
                }
                return $charges;
            }
        }
        return null;
    }

    private static function discounts($inputs)
    {
        if (array_key_exists('discounts', $inputs)) {
            if ($inputs['discounts']) {
                $discounts = [];
                foreach ($inputs['discounts'] as $row) {
                    $discount_type_id = $row['discount_type_id'];
                    $description = $row['description'];
                    $factor = $row['factor'];
                    $amount = $row['amount'];
                    $base = $row['base'];

                    $discounts[] = [
                        'discount_type_id' => $discount_type_id,
                        'description' => $description,
                        'factor' => $factor,
                        'amount' => $amount,
                        'base' => $base,
                    ];
                }
                return $discounts;
            }
        }
        return null;
    }

    private static function prepayments($inputs)
    {
        if (array_key_exists('prepayments', $inputs)) {
            if ($inputs['prepayments']) {
                $prepayments = [];
                foreach ($inputs['prepayments'] as $row) {
                    $number = $row['number'];
                    $document_type_id = $row['document_type_id'];
                    $amount = $row['amount'];
                    $total = $row['total'];

                    $prepayments[] = [
                        'number' => $number,
                        'document_type_id' => $document_type_id,
                        'amount' => $amount,
                        'total' => $total
                    ];
                }
                return $prepayments;
            }
        }
        return null;
    }

    private static function guides($inputs)
    {
        if (array_key_exists('guides', $inputs)) {
            if ($inputs['guides']) {
                $guides = [];
                foreach ($inputs['guides'] as $row) {
                    $number = $row['number'];
                    $document_type_id = $row['document_type_id'];
                    $guides[] = [
                        'number' => $number,
                        'document_type_id' => $document_type_id,
                        'document_type_description' => ucfirst(mb_strtolower(DocumentType::find($document_type_id)->description)),
                    ];
                }
                return $guides;
            }
        }
        return null;
    }

    private static function related($inputs)
    {
        if (array_key_exists('related', $inputs)) {
            if ($inputs['related']) {
                $related = [];
                foreach ($inputs['related'] as $row) {
                    $number = $row['number'];
                    $document_type_id = $row['document_type_id'];
                    $amount = $row['amount'];

                    $related[] = [
                        'number' => $number,
                        'document_type_id' => $document_type_id,
                        'amount' => $amount
                    ];
                }
                return $related;
            }
        }
        return null;
    }

    private static function perception($inputs)
    {
        if (array_key_exists('perception', $inputs)) {
            if ($inputs['perception']) {
                $perception = $inputs['perception'];
                $code = $perception['code'];
                $percentage = $perception['percentage'];
                $amount = $perception['amount'];
                $base = $perception['base'];

                return [
                    'code' => $code,
                    'percentage' => $percentage,
                    'amount' => $amount,
                    'base' => $base,
                ];
            }
        }
        return null;
    }

    private static function detraction($inputs)
    {
        if (array_key_exists('detraction', $inputs)) {
            if ($inputs['detraction']) {

                // dd($inputs['detraction'],$inputs);
                $detraction = $inputs['detraction'];
                $detraction_type_id = $detraction['detraction_type_id'];
                $percentage = $detraction['percentage'];
                $amount = $detraction['amount'];
                $payment_method_id = $detraction['payment_method_id'];
                $bank_account = $detraction['bank_account'];


                //detraction transport
                $reference_value_service = null;
                $reference_value_effective_load = null;
                $reference_value_payload = null;
                $origin_location_id = [];
                $origin_address = null;
                $delivery_location_id = [];
                $delivery_address = null;
                $trip_detail = null;

                if ($inputs['operation_type_id'] === '1004') {

                    $reference_value_service = $detraction['reference_value_service'];
                    $reference_value_effective_load = $detraction['reference_value_effective_load'];
                    $reference_value_payload = $detraction['reference_value_payload'];
                    $origin_location_id = $detraction['origin_location_id'];
                    $origin_address = $detraction['origin_address'];
                    $delivery_location_id = $detraction['delivery_location_id'];
                    $delivery_address = $detraction['delivery_address'];
                    $trip_detail = $detraction['trip_detail'];

                }
                //detraction transport

                $pay_constancy = array_key_exists('pay_constancy', $detraction) ? $detraction['pay_constancy'] : null;
                $set_image_pay_constancy = null;
                $image_pay_constancy = array_key_exists('image_pay_constancy', $detraction) ? $detraction['image_pay_constancy'] : null;

                if (isset($image_pay_constancy['temp_path'])) {

                    $directory = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'image_detractions' . DIRECTORY_SEPARATOR;

                    $file_name_old = $image_pay_constancy['image'];
                    $file_name_old_array = explode('.', $file_name_old);
                    $file_content = file_get_contents($image_pay_constancy['temp_path']);
                    $datenow = date('YmdHis');
                    $file_name = $detraction_type_id . '-' . $bank_account . '-' . $datenow . '.' . $file_name_old_array[1];
                    Storage::put($directory . $file_name, $file_content);
                    $set_image_pay_constancy = $file_name;

                }

                return [
                    'detraction_type_id' => $detraction_type_id,
                    'percentage' => $percentage,
                    'amount' => $amount,
                    'payment_method_id' => $payment_method_id,
                    'bank_account' => $bank_account,
                    'pay_constancy' => $pay_constancy,
                    'image_pay_constancy' => $set_image_pay_constancy,
                    'reference_value_service' => $reference_value_service,
                    'reference_value_effective_load' => $reference_value_effective_load,
                    'reference_value_payload' => $reference_value_payload,
                    'origin_location_id' => $origin_location_id,
                    'origin_address' => $origin_address,
                    'delivery_location_id' => $delivery_location_id,
                    'delivery_address' => $delivery_address,
                    'trip_detail' => $trip_detail,
                ];
            }
        }
        return null;
    }

    private static function hotel($inputs)
    {
        // dd($inputs);
        return key_exists('hotel', $inputs)?$inputs['hotel']:null;
    }

    private static function transport($inputs)
    {
        // dd($inputs);
        if (array_key_exists('transport', $inputs)) {

            return $inputs['transport'];
        }

        return [];
    }

    private static function invoice($inputs)
    {
        $operation_type_id = $inputs['operation_type_id'];
        $date_of_due = $inputs['date_of_due'];

        return [
            'type' => 'invoice',
            'group_id' => ($inputs['document_type_id'] === '01') ? '01' : '02',
            'invoice' => [
                'operation_type_id' => $operation_type_id,
                'date_of_due' => $date_of_due,
            ]
        ];
    }

    private static function note($inputs)
    {
        $document_type_id = $inputs['document_type_id'];
        $note_credit_or_debit_type_id = $inputs['note_credit_or_debit_type_id'];
        $note_description = $inputs['note_description'];
        $affected_document_id = $inputs['affected_document_id'];

        $data_affected_document = Functions::valueKeyInArray($inputs, 'data_affected_document');

        $type = ($document_type_id === '07') ? 'credit' : 'debit';

        if (!$data_affected_document) {

            $affected_document = Document::find($affected_document_id);
            $group_id = $affected_document->group_id;
            $$affected_document_id = $affected_document->id;

        } else {

            $affected_document_id = null;
            $group_id = ($data_affected_document['document_type_id'] == '01') ? '01' : '02';

        }


        return [
            'type' => $type,
            // 'group_id' => $affected_document->group_id,
            'group_id' => $group_id,
            'note' => [
                'note_type' => $type,
                'note_credit_type_id' => ($type === 'credit') ? $note_credit_or_debit_type_id : null,
                'note_debit_type_id' => ($type === 'debit') ? $note_credit_or_debit_type_id : null,
                'note_description' => $note_description,
                'affected_document_id' => $affected_document_id,
                'data_affected_document' => $data_affected_document
            ]
        ];
    }
}
