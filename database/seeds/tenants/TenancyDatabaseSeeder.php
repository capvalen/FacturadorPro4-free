<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TenancyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // $id = DB::table('items')->insertGetId(
        //     ['name' => 'Laptop Razer', 'second_name' => 'Laptop Razer', 'description' => 'Laptop Razer','item_type_id' => '01', 
        //     'unit_type_id' => 'NIU', 'currency_type_id' => 'PEN', 'sale_unit_price' => '5500.00', 'has_igv' => 1, 'purchase_unit_price' => '3000.00', 'has_isc' => 0,
        //     'amount_plastic_bag_taxes' => '0.10', 'percentage_isc' => '0.00', 'suggested_price' => '0.00', 'sale_affectation_igv_type_id' => '10' , 
        //     'purchase_affectation_igv_type_id' => '10', 'calculate_quantity' => '0', 'image' => 'demo1.jpg', 'image_medium' => 'demo1_medium.jpg', 'image_small' => 'demo1_small.jpg',  'stock' => '1',
        //     'stock_min' => '1',  'percentage_of_profit' => '20.0',  'has_perception' => '0',  'status' => 1, 'apply_store' => 1 ]
        // );

        // $id2 = DB::table('items')->insertGetId(
           
        //     ['name' => 'MacBook Pro', 'second_name' => 'LMacBook Pro', 'description' => 'MacBook Pro','item_type_id' => '01', 
        //     'unit_type_id' => 'NIU', 'currency_type_id' => 'PEN', 'sale_unit_price' => '5500.00', 'has_igv' => 1, 'purchase_unit_price' => '3000.00', 'has_isc' => 0,
        //     'amount_plastic_bag_taxes' => '0.10', 'percentage_isc' => '0.00', 'suggested_price' => '0.00', 'sale_affectation_igv_type_id' => '10' , 
        //     'purchase_affectation_igv_type_id' => '10', 'calculate_quantity' => '0', 'image' => 'demo2.jpg', 'image_medium' => 'demo2_medium.jpg', 'image_small' => 'demo2_small.jpg',  'stock' => '1',
        //     'stock_min' => '1',  'percentage_of_profit' => '20.0',  'has_perception' => '0',  'status' => 1, 'apply_store' => 1 ]
        // );

        // $id3 = DB::table('items')->insertGetId(
           
        //     ['name' => 'Laptop Asus', 'second_name' => 'Laptop Asus', 'description' => 'Laptop Asus','item_type_id' => '01', 
        //     'unit_type_id' => 'NIU', 'currency_type_id' => 'PEN', 'sale_unit_price' => '5500.00', 'has_igv' => 1, 'purchase_unit_price' => '3000.00', 'has_isc' => 0,
        //     'amount_plastic_bag_taxes' => '0.10', 'percentage_isc' => '0.00', 'suggested_price' => '0.00', 'sale_affectation_igv_type_id' => '10' , 
        //     'purchase_affectation_igv_type_id' => '10', 'calculate_quantity' => '0', 'image' => 'demo3.jpg', 'image_medium' => 'demo3_medium.jpg', 'image_small' => 'demo3_small.jpg',  'stock' => '1',
        //     'stock_min' => '1',  'percentage_of_profit' => '20.0',  'has_perception' => '0',  'status' => 1, 'apply_store' => 1 ]
         
        // );

        // DB::table('promotions')->insert([
        //     ['name' => 'Promocion 1', 'description' => 'Promocion 1', 'image' => 'promo1.jpg', 'item_id'=> $id, 'status'=> 1 ],
        //     ['name' => 'Promocion 2', 'description' => 'Promocion 2', 'image' => 'promo2.jpg', 'item_id'=> $id2, 'status'=> 1 ],
        //     ['name' => 'Promocion 3', 'description' => 'Promocion 3', 'image' => 'promo3.jpg', 'item_id'=> $id3, 'status'=> 1 ]
        // ]);

        // DB::table('module_user')->insert([
        //     ['module_id' => 10, 'user_id' => 1, ]
        // ]);
         DB::table('format_templates')->insert([
            ['id'=> 1, 'formats' => 'con_valor_unitario'],
            ['id'=> 2, 'formats' => 'default'],
            ['id'=> 3, 'formats' => 'default2'],
            ['id'=> 4, 'formats' => 'font_sm'],
            ['id'=> 5, 'formats' => 'font_sw2'],
            ['id'=> 6, 'formats' => 'legend_amazonia'],
            ['id'=> 7, 'formats' => 'model1'],
            ['id'=> 8, 'formats' => 'model2'],
            ['id'=> 9, 'formats' => 'model3'],
            ['id'=> 10, 'formats' => 'model4'],
            ['id'=> 11, 'formats' => 'modelw80'],
            ['id'=> 12, 'formats' => 'santiago'],
            ['id'=> 13, 'formats' => 'top_placa'],
            ['id'=> 14, 'formats' => 'unit_types_desc']
        ]);
    }
}
