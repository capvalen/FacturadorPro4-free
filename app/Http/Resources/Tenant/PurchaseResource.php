<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\Purchase;
use Modules\Inventory\Models\Warehouse;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $purchase = Purchase::find($this->id);
        $purchase->purchase_payments = self::getTransformPayments($purchase->purchase_payments);
        $purchase->items = self::getTransformItems($purchase->items);
        $purchase->customer_number = $purchase->customer_id ? $purchase->customer->number:null;

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'group_id' => $this->group_id,
            'number' => $this->number_full,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'purchase' => $purchase
        ];
    }


    public static function getTransformPayments($payments){

        return $payments->transform(function($row, $key){
            return [
                'id' => $row->id,
                'purchase_id' => $row->purchase_id,
                'date_of_payment' => $row->date_of_payment->format('Y-m-d'),
                'payment_method_type_id' => $row->payment_method_type_id,
                'has_card' => $row->has_card,
                'card_brand_id' => $row->card_brand_id,
                'reference' => $row->reference,
                'payment' => $row->payment,
                'payment_method_type' => $row->payment_method_type,
                'payment_destination_id' => ($row->global_payment) ? ($row->global_payment->type_record == 'cash' ? 'cash':$row->global_payment->destination_id):null,
                'payment_filename' => ($row->payment_file) ? $row->payment_file->filename:null,
            ];
        });

    }


    public static function getTransformItems($items){

        return $items->transform(function($row, $key){
            return [
                'id' => $row->id,
                'purchase_id' => $row->purchase_id,
                'item_id' => $row->item_id,
                'item' => $row->item,
                'lot_code' => $row->lot_code,
                'quantity' => $row->quantity,
                'unit_value' => $row->unit_value,
                'date_of_due' => $row->date_of_due,
                'affectation_igv_type_id' => $row->affectation_igv_type_id,
                'total_base_igv' => $row->total_base_igv,
                'percentage_igv' => $row->percentage_igv,
                'total_igv' => $row->total_igv,
                'system_isc_type_id' => $row->system_isc_type_id,
                'total_base_isc' => $row->total_base_isc,
                'percentage_isc' => $row->percentage_isc,
                'total_isc' => $row->total_isc,
                'total_base_other_taxes' => $row->total_base_other_taxes,
                'percentage_other_taxes' => $row->percentage_other_taxes,
                'total_other_taxes' => $row->total_other_taxes,
                'total_taxes' => $row->total_taxes,
                'price_type_id' => $row->price_type_id,
                'unit_price' => $row->unit_price,
                'total_value' => $row->total_value,
                'total_charge' => $row->total_charge,
                'total_discount' => $row->total_discount,
                'total' => $row->total,
                'attributes' => $row->attributes,
                'discounts' => $row->discounts,
                'charges' => $row->charges,
                'warehouse_id' => $row->warehouse_id,
                'affectation_igv_type' => $row->affectation_igv_type,
                'system_isc_type' => $row->system_isc_type,
                'price_type' => $row->price_type,
                'lots' => $row->lots,
                'warehouse' => ($row->warehouse) ? $row->warehouse :  self::getWarehouse($row->purchase->establishment_id),
            ];
        });

    }

    public static function getWarehouse($establishment_id){
        return Warehouse::where('establishment_id', $establishment_id)->first();
    }

}
