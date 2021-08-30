<?php

namespace Modules\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Requests\Api\InventoryRequest;
use Modules\Item\Models\ItemLot;
use Modules\Item\Models\ItemLotsGroup;
use App\Models\Tenant\Item;

class InventoryController extends Controller
{
    use InventoryTrait;
 

    public function store_transaction(InventoryRequest $request)
    {

        $result = DB::connection('tenant')->transaction(function () use ($request) {

            $item = Item::select('id')->where('internal_id', $request->item_code)->first();

            if(!$item){
                return [
                    'success' => false,
                    'message' => 'No se encontró un producto con el codigo ingresado'
                ];
            }

            $item_id = $item->id;

            $type = $request->input('type');
            $warehouse_id = $request->input('warehouse_id');
            $inventory_transaction_id = $request->input('inventory_transaction_id');
            $quantity = $request->input('quantity');

            // $lot_code = $request->input('lot_code');
            // $lots = ($request->has('lots')) ? $request->input('lots'):[];

            $item_warehouse = ItemWarehouse::firstOrNew(['item_id' => $item_id,
                                                         'warehouse_id' => $warehouse_id]);

            $inventory_transaction = InventoryTransaction::findOrFail($inventory_transaction_id);

            if($type == 'output' && ($quantity > $item_warehouse->stock)) {
                return  [
                    'success' => false,
                    'message' => 'La cantidad no puede ser mayor a la que se tiene en el almacén.'
                ];
            }

            $inventory = new Inventory();
            $inventory->type = null;
            $inventory->description = $inventory_transaction->name;
            $inventory->item_id = $item_id;
            $inventory->warehouse_id = $warehouse_id;
            $inventory->quantity = $quantity;
            $inventory->inventory_transaction_id = $inventory_transaction_id;
            // $inventory->lot_code = $lot_code;
            $inventory->save();


            // $lots_enabled = isset($request->lots_enabled) ? $request->lots_enabled:false;

            // if($type == 'input'){

            //     foreach ($lots as $lot){
 
            //         $inventory->lots()->create([
            //             'date' => $lot['date'],
            //             'series' => $lot['series'],
            //             'item_id' => $item_id,
            //             'warehouse_id' => $warehouse_id,
            //             'has_sale' => false,
            //             'state' => $lot['state'],
            //         ]);

            //     }

            //     if($lots_enabled)
            //     {
            //         ItemLotsGroup::create([
            //             'code'  => $lot_code,
            //             'quantity'  => $quantity,
            //             'date_of_due'  => $request->date_of_due,
            //             'item_id' => $item_id
            //         ]);
            //     }


            // }else{

            //     foreach ($lots as $lot){

            //         if($lot['has_sale']){

            //             $item_lot = ItemLot::findOrFail($lot['id']);
            //             // $item_lot->delete();
            //             $item_lot->has_sale = true;
            //             $item_lot->state = 'Inactivo';
            //             $item_lot->save();
            //         }

            //     }

            //     if(isset($request->IdLoteSelected))
            //     {
            //         $lot = ItemLotsGroup::find($request->IdLoteSelected);
            //         $lot->quantity = ($lot->quantity - $quantity);
            //         $lot->save();
            //     }


            // }

            return  [
                'success' => true,
                'message' => ($type == 'input') ? 'Ingreso registrado correctamente' : 'Salida registrada correctamente'
            ];
        });

        return $result;
    }

 


}
