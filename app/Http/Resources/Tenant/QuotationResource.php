<?php

namespace App\Http\Resources\Tenant;

use App\Models\Tenant\Quotation;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Tenant\Item;
use Modules\Inventory\Models\Warehouse as ModuleWarehouse;


class QuotationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $quotation = Quotation::find($this->id);
        $quotation->payments = self::getTransformPayments($quotation->payments);
        $quotation->items = self::getTransformItems($quotation->items);


        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'identifier' => $this->identifier,
            'date_of_issue' => $this->date_of_issue->format('Y-m-d'),
            'quotation' => $quotation
        ];
    }


    public static function getTransformPayments($payments){

        return $payments->transform(function($row, $key){
            return [
                'id' => $row->id,
                'quotation_id' => $row->quotation_id,
                'date_of_payment' => $row->date_of_payment->format('Y-m-d'),
                'payment_method_type_id' => $row->payment_method_type_id,
                'has_card' => $row->has_card,
                'card_brand_id' => $row->card_brand_id,
                'reference' => $row->reference,
                'payment' => $row->payment,
                'payment_method_type' => $row->payment_method_type,
                'payment_destination_id' => ($row->global_payment) ? ($row->global_payment->type_record == 'cash' ? 'cash':$row->global_payment->destination_id):null,
            ];
        });

    }

    public static function getTransformItems($items)
    {
        $establishment_id = auth()->user()->establishment_id;
        $warehouse_id = ModuleWarehouse::where('establishment_id', $establishment_id)->first()->id;

        return $items->transform(function($row, $key) use ($warehouse_id){
            $row['item'] = self::getTransformItem($row['item'], $warehouse_id);
            return $row;
        });
    }

    public static function getTransformItem($item, $warehouse_id)
    {
        $resource = Item::find($item->id);

        $data_lots = [
            'lots' => [],
            // 'lots' => $resource->item_lots->where('has_sale', false)->where('warehouse_id', $warehouse_id)->transform(function($row) {
            //     return [
            //         'id' => $row->id,
            //         'series' => $row->series,
            //         'date' => $row->date,
            //         'item_id' => $row->item_id,
            //         'warehouse_id' => $row->warehouse_id,
            //         'has_sale' => (bool)$row->has_sale,
            //         'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
            //     ];
            // })->values(),
            'series_enabled' => (bool) $resource->series_enabled,
        ];

        //asigno nuevos valores a item
        $item->series_enabled = $data_lots['series_enabled'];
        $item->lots = $data_lots['lots'];

        return $item;
    }


}
