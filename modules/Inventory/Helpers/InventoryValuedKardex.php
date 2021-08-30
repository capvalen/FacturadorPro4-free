<?php

namespace Modules\Inventory\Helpers;


class InventoryValuedKardex
{

    public static function getTransformRecords($records)
    {
        
        return $records->transform(function($row, $key){
            
            $values_records = self::getValuesRecords($row->document_items, $row->sale_note_items);

            $quantity_sale = $values_records['quantity_sale'];
            $total_sales = $values_records['total_sales'];

            $item_cost = $quantity_sale * $row->purchase_unit_price;
            $valued_unit = $total_sales - $item_cost;

            return [

                'id' => $row->id,
                'item_description' => $row->description,
                'category_description' => optional($row->category)->name,
                'brand_description' => optional($row->brand)->name,
                'unit_type_id' => $row->unit_type_id,
                'quantity_sale' => number_format($quantity_sale,2, ".", ""),
                'purchase_unit_price' => number_format($row->purchase_unit_price,2, ".", ""),
                'total_sales' => number_format($total_sales,2, ".", ""),
                'item_cost' => number_format($item_cost,2, ".", ""),
                'valued_unit' => number_format($valued_unit,2, ".", ""),
                'warehouses' => $row->warehouses->transform(function($row, $key){
                    return [
                        'id' => $row->id,
                        'stock' => $row->stock,
                        'warehouse_description' => $row->warehouse->description,
                        'description' => "{$row->warehouse->description} | {$row->stock}",
                    ];
                }),

            ];
            
        });

    }

    public static function getValuesRecords($document_items, $sale_note_items)
    {

        //quantity
        $quantity_doc_items = $document_items->sum(function($row){
            return ($row->document->document_type_id == '07') ? -$row->quantity : $row->quantity;
        });

        $quantity_sln_items = $sale_note_items->sum('quantity');

        $quantity_sale = $quantity_doc_items + $quantity_sln_items;


        //totals
        $sales_documents = $document_items->sum(function($row){
            $value_currency = self::calculateTotalCurrencyType($row->document, $row->total);
            return ($row->document->document_type_id == '07') ? -$value_currency : $value_currency;
        });

        $sales_sale_notes = $sale_note_items->sum(function($row){
            return self::calculateTotalCurrencyType($row->sale_note, $row->total);
        });

        $total_sales = $sales_documents + $sales_sale_notes;


        return [
            'quantity_sale' => $quantity_sale,
            'total_sales' => $total_sales,
        ];

    }


    public static function calculateTotalCurrencyType($record, $amount)
    {
        return ($record->currency_type_id === 'USD') ? $amount * $record->exchange_rate_sale : $amount;
    }


 
}