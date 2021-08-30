<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'technical_specifications' => $this->technical_specifications,
            'name' => $this->name,
            'second_name' => $this->second_name,
            'model' => $this->model,
            'barcode' => $this->barcode,
            'warehouse_id' => $this->warehouse_id,
            'internal_id' => $this->internal_id,
            'item_code' => $this->item_code,
            'item_code_gsl' => $this->item_code_gsl,
            'currency_type_id' => $this->currency_type_id,
            'sale_unit_price' => $this->sale_unit_price,
            'purchase_unit_price' => $this->purchase_unit_price,
            'unit_type_id' => $this->unit_type_id,
            'has_isc' => (bool) $this->has_isc,
            'system_isc_type_id' => $this->system_isc_type_id,
            'percentage_isc' => $this->percentage_isc,
            'suggested_price' => $this->suggested_price,
            'stock' => $this->getStockByWarehouse(),
            'stock_min' => $this->stock_min,
            'percentage_of_profit' => $this->percentage_of_profit,
            'sale_affectation_igv_type_id' => $this->sale_affectation_igv_type_id,
            'purchase_affectation_igv_type_id' => $this->purchase_affectation_igv_type_id,
            'calculate_quantity' => (bool) $this->calculate_quantity,
            'has_igv' => (bool) $this->has_igv,
            'purchase_has_igv' => (bool) $this->purchase_has_igv,
            'has_perception' => (bool) $this->has_perception,
            'lots_enabled' => (bool) $this->lots_enabled,
            'percentage_perception' => $this->percentage_perception,
            'item_unit_types' => $this->item_unit_types,
            'image' => $this->image,
            'account_id' => $this->account_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'date_of_due' => !empty($this->date_of_due) ? $this->date_of_due->format('Y-m-d H:i:s') : null,
            'image_url' => ($this->image !== 'imagen-no-disponible.jpg') ? asset('storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR.$this->image) : asset("/logo/{$this->image}"),
            'apply_store' => (bool)$this->apply_store,
            'has_plastic_bag_taxes' => (bool)$this->has_plastic_bag_taxes,
            'tags' => $this->tags,
            'tags_id' => $this->tags->pluck('tag_id'),
            // 'individual_items' => collect($this->sets)->pluck('individual_item_id'),
            'commission_amount' => $this->commission_amount,
            'lot_code' => $this->lot_code,
            'line' => $this->line,
            'lots' => $this->lots->transform(function($row, $key) {
                return [
                    'id' => $row->id,
                    'series' => $row->series,
                    'date' => $row->date,
                    'item_id' => $row->item_id,
                    'warehouse_id' => $row->warehouse_id,
                    'item_loteable_type' => $row->item_loteable_type,
                    'item_loteable_id' => $row->item_loteable_id,
                    'has_sale' => $row->has_sale,
                    'state' => $row->state,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'deleted' => false
                ];
            }),
            'commission_type' => $this->commission_type ?? 'amount',
            'attributes' => $this->attributes ? $this->attributes : [],
            'series_enabled' => (bool)$this->series_enabled,
            'lots_enabled' => (bool)$this->lots_enabled,
            'individual_items' => $this->sets->transform(function($row, $key) {

                $full_description = ($row->individual_item->internal_id)?$row->individual_item->internal_id.' - '.$row->individual_item->description:$row->individual_item->description;

                return [
                    'id' => $row->id,
                    'item_id' => $row->item_id,
                    'individual_item_id' => $row->individual_item_id,
                    'full_description' => $full_description,
                    'sale_unit_price' => (float) $row->individual_item->sale_unit_price,
                    'quantity' => (float) $row->quantity,
                ];
            }),
            'web_platform_id' => $this->web_platform_id,

            // 'warehouses' => collect($this->warehouses)->transform(function($row) {
            //     return [
            //         'warehouse_description' => $row->warehouse->description,
            //         'stock' => $row->stock,
            //     ];
            // })
            'warehouse_prices' => $this->warehousePrices,
        ];
    }
}
