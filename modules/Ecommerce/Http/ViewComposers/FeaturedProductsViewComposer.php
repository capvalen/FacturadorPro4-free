<?php

namespace Modules\Ecommerce\Http\ViewComposers;


use App\Models\Tenant\Item;
//use App\Http\Resources\Tenant\ItemEcommerceCollection;
use App\Http\Controllers\Tenant\Api\ServiceController;


class FeaturedProductsViewComposer
{
    public function compose($view)
    {

        $exchange_rate_sale = $this->getExchangeRateSale();

        $view->items = Item::where([['apply_store', 1], ['internal_id','!=', null]])->get()->transform(function($row, $key) use($exchange_rate_sale){

            $sale_unit_price = ($row->has_igv)? $row->sale_unit_price:$row->sale_unit_price*1.18;

            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();
            $months = $date->diffInMonths($row->created_at);

            return (object)[
                'id' => $row->id,
                'internal_id' => $row->internal_id,
                'unit_type_id' => $row->unit_type_id,
                'description' => $row->description,
                'name' => $row->name,
                'second_name' => $row->second_name,
                'sale_unit_price' => ($row->currency_type_id === 'PEN') ? $sale_unit_price : $sale_unit_price * $exchange_rate_sale,
                'sale_unit' => $sale_unit_price,
                'currency_type_id' => $row->currency_type_id,
                'has_igv' => (bool) $row->has_igv,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'currency_type_symbol' => $row->currency_type->symbol,
                'image' =>  $row->image,
                'image_medium' => $row->image_medium,
                'image_small' => $row->image_small,
                'tags' => $row->tags->pluck('tag_id')->toArray(),
                'is_new' => ($months > 1) ? 0 : 1,
                /*'multi_images'  => $row->images->transform(function($r){
                    return [
                        $r->image
                    ];
                })*/
            ];
        });
    }

    private function getExchangeRateSale(){

        $exchange_rate = app(ServiceController::class)->exchangeRateTest(date('Y-m-d'));

        return (array_key_exists('sale', $exchange_rate)) ? $exchange_rate['sale'] : 1;

    }

}
