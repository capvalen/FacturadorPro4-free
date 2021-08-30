<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseCollection extends ResourceCollection
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

            $total = $row->total;
            if($row->total_perception)
            {
                $total += round($row->total_perception, 2);
            }
            return [
                'id' => $row->id,
                'document_type_description' => $row->document_type->description,
                'group_id' => $row->group_id,
                'soap_type_id' => $row->soap_type_id,
                'date_of_issue' => $row->date_of_issue->format('Y-m-d'),
                'date_of_due' => ($row->date_of_due) ? $row->date_of_due->format('Y-m-d'):'-',
                'number' => $row->number_full,
                'supplier_name' => $row->supplier->name,
                'supplier_number' => $row->supplier->number,
                'currency_type_id' => $row->currency_type_id,
                'total_exportation' => $row->total_exportation,
                'total_free' => number_format($row->total_free, 2, ".",""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".",""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".",""),
                'total_taxed' => number_format($row->total_taxed, 2, ".",""),
                'total_igv' => number_format($row->total_igv, 2, ".",""),
                'total_perception' => number_format($row->total_perception, 2, ".",""),
                'total' => number_format($total, 2, ".",""),
                'state_type_id' => $row->state_type_id,
                'state_type_description' => $row->state_type->description,
                'state_type_payment_description' => $row->total_canceled ? 'Pagado':'Pendiente de pago',
                // 'payment_method_type_description' => isset($row->purchase_payments['payment_method_type']['description'])?$row->purchase_payments['payment_method_type']['description']:'-',
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $row->updated_at->format('Y-m-d H:i:s'),
                'payments' => $row->purchase_payments->transform(function($row, $key) {
                    return [
                        'id' => $row->id,
                        'payment_method_type_description' => $row->payment_method_type->description,
                        'reference' => $row->reference,
                        'payment' => $row->payment,
                        'payment_method_type_id' => $row->payment_method_type_id,
                    ];
                }),
                'items' => $row->items->transform(function($row, $key) {
                    return [
                        'key' => $key + 1,
                        'id' => $row->id,
                        'description' => $row->item->description,
                        'quantity' => round($row->quantity,2)
                    ];
                }),
                'print_a4' => url('')."/purchases/print/{$row->external_id}/a4",
            ];
        });
    }

}
