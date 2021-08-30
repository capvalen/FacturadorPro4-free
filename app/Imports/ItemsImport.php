<?php

namespace App\Imports;

use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Item\Models\Category;
use Modules\Item\Models\Brand;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class ItemsImport implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $registered = 0;
            unset($rows[0]);
            foreach ($rows as $row) {
                $description = $row[0];
                $item_type_id = '01';
                $internal_id = ($row[1])?:null;
                $model = ($row[2]) ? : null;
                $item_code = ($row[3])?:null;
                $unit_type_id = $row[4];
                $currency_type_id = $row[5];
                $sale_unit_price = $row[6];
                $sale_affectation_igv_type_id = $row[7];
                // $has_igv = (strtoupper($row[7]) === 'SI')?true:false;

                $affectation_igv_types_exonerated_unaffected = ['20','21','30','31','32','33','34','35','36','37'];

                if(in_array($sale_affectation_igv_type_id, $affectation_igv_types_exonerated_unaffected)) {

                    $has_igv = true;

                }else{

                    $has_igv = (strtoupper($row[8]) === 'SI')?true:false;

                }

                $purchase_unit_price = ($row[9])?:0;
                $purchase_affectation_igv_type_id = ($row[10])?:null;
                $stock = $row[11];
                $stock_min = $row[12];
                $category_name = $row[13];
                $brand_name = $row[14];

                $name = $row[15];
                $second_name = $row[16];

                $lot_code = $row[17];
                $date_of_due = $row[18];
                $barcode = $row[19] ?? null;
                $warehouse_id = request('warehouse_id');


                if($internal_id) {
                    $item = Item::where('internal_id', $internal_id)
                                    ->first();
                } else {
                    $item = null;
                }
                // $establishment_id = auth()->user()->establishment->id;

                if(!$item) {
                    $category = $category_name ? Category::updateOrCreate(['name' => $category_name]):null;
                    $brand = $brand_name ? Brand::updateOrCreate(['name' => $brand_name]):null;
                    // dd($row, $lot_code ,$date_of_due, $category, $brand);

                    if($lot_code && $date_of_due){

                        $_date_of_due = Date::excelToDateTimeObject($date_of_due)->format('Y-m-d');

                        // dd($lot_code, $date_of_due, $x);

                        $new_item = Item::create([
                            'description' => $description,
                            'model' => $model,
                            'item_type_id' => $item_type_id,
                            'internal_id' => $internal_id,
                            'item_code' => $item_code,
                            'unit_type_id' => $unit_type_id,
                            'currency_type_id' => $currency_type_id,
                            'sale_unit_price' => $sale_unit_price,
                            'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                            'has_igv' => $has_igv,
                            'purchase_unit_price' => $purchase_unit_price,
                            'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                            'stock' => $stock,
                            'stock_min' => $stock_min,
                            'category_id' => optional($category)->id,
                            'brand_id' => optional($brand)->id,
                            'name' => $name,
                            'second_name' => $second_name,

                            'lots_enabled' => true,
                            'lot_code' => $lot_code,
                            'date_of_due' => $_date_of_due,
                            'barcode' => $barcode,
                            'warehouse_id' => $warehouse_id,
                        ]);

                        $new_item->lots_group()->create([
                            'code'  => $lot_code,
                            'quantity'  => $stock,
                            'date_of_due'  => $_date_of_due,
                        ]);

                    }else{

                        Item::create([
                            'description' => $description,
                            'model' => $model,
                            'item_type_id' => $item_type_id,
                            'internal_id' => $internal_id,
                            'item_code' => $item_code,
                            'unit_type_id' => $unit_type_id,
                            'currency_type_id' => $currency_type_id,
                            'sale_unit_price' => $sale_unit_price,
                            'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                            'has_igv' => $has_igv,
                            'purchase_unit_price' => $purchase_unit_price,
                            'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                            'stock' => $stock,
                            'stock_min' => $stock_min,
                            'category_id' => optional($category)->id,
                            'brand_id' => optional($brand)->id,
                            'name' => $name,
                            'second_name' => $second_name,
                            'barcode' => $barcode,
                            'warehouse_id' => $warehouse_id,
                        ]);

                    }


                    $registered += 1;

                }else{
                    $item->update([
                        'description' => $description,
                        'model' => $model,
                        'item_type_id' => $item_type_id,
                        'internal_id' => $internal_id,
                        'item_code' => $item_code,
                        'unit_type_id' => $unit_type_id,
                        'currency_type_id' => $currency_type_id,
                        'sale_unit_price' => $sale_unit_price,
                        'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                        'has_igv' => $has_igv,
                        'purchase_unit_price' => $purchase_unit_price,
                        'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                        'stock_min' => $stock_min,
                        'name' => $name,
                        'second_name' => $second_name,
                        'barcode' => $barcode,
                    ]);

                    $registered += 1;

                }
            }
            $this->data = compact('total', 'registered');

    }

    public function getData()
    {
        return $this->data;
    }
}
