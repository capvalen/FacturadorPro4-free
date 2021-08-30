<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SaleNoteCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function($row, $key) {

            $total_paid = number_format($row->payments->sum('payment'), 2, ".", "");
            $total_pending_paid = number_format($row->total - $total_paid, 2, ".", "");

            $btn_generate = (count($row->documents) > 0)?false:true;
            $btn_payments = (count($row->documents) > 0)?false:true;
            $due_date = (!empty($row->due_date)) ? $row->due_date->format('Y-m-d') : null;
            return [
                'id' => $row->id,
                'soap_type_id' => $row->soap_type_id,
                'external_id' => $row->external_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'identifier' => $row->identifier,
                'full_number' => $row->series.'-'.$row->number,
                'customer_name' => $row->customer->name,
                'customer_number' => $row->customer->number,
                'currency_type_id' => $row->currency_type_id,
                'total_exportation' => number_format($row->total_exportation,2),
                'total_free' => number_format($row->total_free,2),
                'total_unaffected' => number_format($row->total_unaffected,2),
                'total_exonerated' => number_format($row->total_exonerated,2),
                'total_taxed' => number_format($row->total_taxed,2),
                'total_igv' => number_format($row->total_igv,2),
                'total' => number_format($row->total,2),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'documents' => $row->documents->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'number_full' => $row->number_full,
                    ];
                }),
                'btn_generate' => $btn_generate,
                'btn_payments' => $btn_payments,
                'changed' => (boolean) $row->changed,
                'enabled_concurrency' => (boolean) $row->enabled_concurrency,
                'quantity_period' => $row->quantity_period,
                'type_period' => $row->type_period,
                'apply_concurrency' => (boolean) $row->apply_concurrency,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
                'paid' => (bool)$row->paid,
                'total_canceled' => (bool)$row->total_canceled,
                'license_plate' => $row->license_plate,
                'total_paid' => $total_paid,
                'total_pending_paid' => $total_pending_paid,
                'user_name' => ($row->user) ? $row->user->name : '',
                'quotation_number_full' => ($row->quotation) ? $row->quotation->number_full : '',
                'sale_opportunity_number_full' => isset($row->quotation->sale_opportunity) ? $row->quotation->sale_opportunity->number_full : '',
                'number_full' => $row->number_full,
                'print_a4' => url('')."/sale-notes/print/{$row->external_id}/a4",
                'purchase_order' => $row->purchase_order,
                'due_date' => $due_date,
            ];
        });
    }

}
