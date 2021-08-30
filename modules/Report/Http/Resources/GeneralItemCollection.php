<?php

namespace Modules\Report\Http\Resources;

use App\Models\Tenant\PurchaseItem;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GeneralItemCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return $this->collection->transform(function ($row, $key) {
            $resource = self::getDocument($row);
            $total_item_purchase = self::getPurchaseUnitPrice($row);
            $utility_item = $row->total - $total_item_purchase;

            return [
                'id' => $row->id,
                'unit_type_id' => $row->item->unit_type_id,
                'internal_id' => $row->relation_item->internal_id,
                'description' => $row->item->description,
                'currency_type_id' => $resource['currency_type_id'],

                'lot_has_sale' => self::getLotsHasSale($row),

                'date_of_issue' => $resource['date_of_issue'],
                'customer_name' => $resource['customer_name'],
                'customer_number' => $resource['customer_number'],
                'brand' => $row->relation_item->brand->name,

                'series' => $resource['series'],
                'alone_number' => $resource['alone_number'],
                'quantity' => number_format($row->quantity, 2),

                'unit_value' => number_format($row->unit_value, 2),

                'total' => number_format($row->total, 2),
                'total_number' => $row->total,

                'total_item_purchase' => number_format($total_item_purchase, 2),
                'utility_item' => number_format($utility_item, 2),

                'document_type_description' => $resource['document_type_description'],
                'document_type_id' => $resource['document_type_id'],
                'web_platform_name' => optional($row->relation_item->web_platform)->name,
            ];
        });
    }

    public static function getPurchaseUnitPrice($record)
    {

        $purchase_unit_price = 0;

        if ($record->relation_item->is_set) {

            foreach ($record->relation_item->sets as $item_set) {
                $purchase_unit_price += (self::getIndividualPurchaseUnitPrice($item_set) * $item_set->quantity) * $record->quantity;
            }

        } else {

            $purchase_unit_price = self::getIndividualPurchaseUnitPrice($record) * $record->quantity;

        }

        return $purchase_unit_price;
    }

    public static function getIndividualPurchaseUnitPrice($record)
    {

        $purchase_unit_price = 0;

        $purchase_item = PurchaseItem::select('unit_price')->where('item_id', $record->item_id)->latest('id')->first();
        $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : 0;
        // TODO: revisar esta linea: Eliminando esta linea porque el precio de compra no puede ser igual al precio de venta, en conculusión esta condición nunca será 0, para los productos que no tienen una compra luego de registrarse
        // $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : $record->unit_price;

        if ($purchase_unit_price == 0 && $record->relation_item->purchase_unit_price > 0) {
            $purchase_unit_price = $record->relation_item->purchase_unit_price;
        }

        // if ($record->relation_item->purchase_unit_price > 0) {
        //     $purchase_unit_price = $record->relation_item->purchase_unit_price;
        // } else {
        //     $purchase_item = PurchaseItem::select('unit_price')->where('item_id', $record->item_id)->latest('id')->first();
        //     $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : $record->unit_price;
        // }
        return $purchase_unit_price;
    }

    public static function getLotsHasSale($row)
    {
        if (isset($row->item->lots)) {
            return collect($row->item->lots)->where('has_sale', 1);
        } else {
            return [];
        }
    }

    public static function getDocument($row)
    {

        $data = [];
        /*$data['quantity'] = number_format($row->quantity,2);
        $data['total'] = number_format($row->total,2);
        $data['unit_type_id'] = $row->item->unit_type_id;
        $data['description'] = $row->item->description;*/

        if ($row->document) {

            $data['date_of_issue'] = $row->document->date_of_issue->format('Y-m-d');
            $data['customer_name'] = $row->document->customer->name;
            $data['customer_number'] = $row->document->customer->number;
            $data['series'] = $row->document->series;
            $data['alone_number'] = $row->document->number;
            $data['document_type_description'] = $row->document->document_type->description;
            $data['document_type_id'] = $row->document->document_type->id;
            $data['currency_type_id'] = $row->document->currency_type_id;

        } else if ($row->purchase) {
            $data['date_of_issue'] = $row->purchase->date_of_issue->format('Y-m-d');
            $data['customer_name'] = $row->purchase->supplier->name;
            $data['customer_number'] = $row->purchase->supplier->number;
            $data['series'] = $row->purchase->series;
            $data['alone_number'] = $row->purchase->number;
            $data['document_type_description'] = $row->purchase->document_type->description;
            $data['document_type_id'] = $row->purchase->document_type->id;
            $data['currency_type_id'] = $row->purchase->currency_type_id;

        } else if ($row->sale_note) {
            $data['date_of_issue'] = $row->sale_note->date_of_issue->format('Y-m-d');
            $data['customer_name'] = $row->sale_note->customer->name;
            $data['customer_number'] = $row->sale_note->customer->number;
            $data['series'] = $row->sale_note->series;
            $data['alone_number'] = $row->sale_note->number;
            $data['document_type_description'] = 'NOTA DE VENTA';
            $data['document_type_id'] = 80;
            $data['currency_type_id'] = $row->sale_note->currency_type_id;
        }

        return $data;
    }

}
