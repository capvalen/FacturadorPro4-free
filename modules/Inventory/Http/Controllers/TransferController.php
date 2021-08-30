<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Http\Resources\TransferCollection;
use Modules\Inventory\Http\Resources\TransferResource;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Models\InventoryTransfer;
use Modules\Inventory\Http\Requests\InventoryRequest;
use Modules\Inventory\Http\Requests\TransferRequest;

use Modules\Item\Models\ItemLot;

class TransferController extends Controller
{
    use InventoryTrait;

    public function index()
    {
        return view('inventory::transfers.index');
    }

    public function create()
    {
       // $establishment_id = auth()->user()->establishment_id;
        //$current_warehouse = Warehouse::where('establishment_id', $establishment_id)->first();
        return view('inventory::transfers.form');

    }

    public function columns()
    {
        return [
            'created_at' => 'Fecha de emisión',
        ];
    }

    public function records(Request $request)
    {
        if($request->column)
        {
            $records = InventoryTransfer::with(['warehouse','warehouse_destination', 'inventory'])->where('created_at', 'like', "%{$request->value}%")->latest();
        }
        else{
            $records = InventoryTransfer::with(['warehouse','warehouse_destination', 'inventory'])->latest();

        }
        //return json_encode( $records );
        /*$records = Inventory::with(['item', 'warehouse', 'warehouse_destination'])
                            ->where('type', 2)
                            ->whereHas('warehouse_destination')
                            ->whereHas('item', function($query) use($request) {
                                $query->where('description', 'like', '%' . $request->value . '%');

                            })
                            ->latest();*/


        return new TransferCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function tables()
    {
        return [
            //'items' => $this->optionsItemWareHouse(),
            'warehouses' => $this->optionsWarehouse()
        ];
    }

    public function record($id)
    {
        $record = new TransferResource(Inventory::findOrFail($id));

        return $record;
    }


   /* public function store(Request $request)
    {

        $result = DB::connection('tenant')->transaction(function () use ($request) {

            $id = $request->input('id');
            $item_id = $request->input('item_id');
            $warehouse_id = $request->input('warehouse_id');
            $warehouse_destination_id = $request->input('warehouse_destination_id');
            $stock = $request->input('stock');
            $quantity = $request->input('quantity');
            $detail = $request->input('detail');

            if($warehouse_id === $warehouse_destination_id) {
                return  [
                    'success' => false,
                    'message' => 'El almacén destino no puede ser igual al de origen'
                ];
            }
            if($stock < $quantity) {
                return  [
                    'success' => false,
                    'message' => 'La cantidad a trasladar no puede ser mayor al que se tiene en el almacén.'
                ];
            }

            $re_it_warehouse = ItemWarehouse::where([['item_id',$item_id],['warehouse_id', $warehouse_destination_id]])->first();

            if(!$re_it_warehouse) {
                return  [
                    'success' => false,
                    'message' => 'El producto no se encuentra registrado en el almacén destino.'
                ];
            }


            $inventory = Inventory::findOrFail($id);

            //proccess stock
            $origin_inv_kardex = $inventory->inventory_kardex->first();
            $origin_item_warehouse = ItemWarehouse::where([['item_id',$origin_inv_kardex->item_id],['warehouse_id', $origin_inv_kardex->warehouse_id]])->first();
            $origin_item_warehouse->stock += $inventory->quantity;
            $origin_item_warehouse->stock -= $quantity;
            $origin_item_warehouse->update();


            $destination_inv_kardex = $inventory->inventory_kardex->last();
            $destination_item_warehouse = ItemWarehouse::where([['item_id',$destination_inv_kardex->item_id],['warehouse_id', $destination_inv_kardex->warehouse_id]])->first();
            $destination_item_warehouse->stock -= $inventory->quantity;
            $destination_item_warehouse->update();


            $new_item_warehouse = ItemWarehouse::where([['item_id',$item_id],['warehouse_id', $warehouse_destination_id]])->first();
            $new_item_warehouse->stock += $quantity;
            $new_item_warehouse->update();

            //proccess stock

            //proccess kardex
            $origin_inv_kardex->quantity = -$quantity;
            $origin_inv_kardex->update();

            $destination_inv_kardex->quantity = $quantity;
            $destination_inv_kardex->warehouse_id = $warehouse_destination_id;
            $destination_inv_kardex->update();
            //proccess kardex

            $inventory->warehouse_destination_id = $warehouse_destination_id;
            $inventory->quantity = $quantity;
            $inventory->detail = $detail;


            $inventory->update();

            return  [
                'success' => true,
                'message' => 'Traslado actualizado con éxito'
            ];
        });

        return $result;
    }*/


    public function destroy($id)
    {

        DB::connection('tenant')->transaction(function () use ($id) {

            $record = Inventory::findOrFail($id);

            $origin_inv_kardex = $record->inventory_kardex->first();
            $destination_inv_kardex = $record->inventory_kardex->last();

            $destination_item_warehouse = ItemWarehouse::where([['item_id',$destination_inv_kardex->item_id],['warehouse_id', $destination_inv_kardex->warehouse_id]])->first();
            $destination_item_warehouse->stock -= $record->quantity;
            $destination_item_warehouse->update();

            $origin_item_warehouse = ItemWarehouse::where([['item_id',$origin_inv_kardex->item_id],['warehouse_id', $origin_inv_kardex->warehouse_id]])->first();
            $origin_item_warehouse->stock += $record->quantity;
            $origin_item_warehouse->update();

            $record->inventory_kardex()->delete();
            $record->delete();

        });


        return [
            'success' => true,
            'message' => 'Traslado eliminado con éxito'
        ];



    }

    public function stock ($item_id, $warehouse_id)
    {

       $row = ItemWarehouse::where([['item_id', $item_id],['warehouse_id', $warehouse_id]])->first();

       return [
           'stock' => ($row) ? $row->stock : 0
       ];

    }

    public function store(TransferRequest $request)
    {
        $result = DB::connection('tenant')->transaction(function () use ($request) {

            $row = InventoryTransfer::create([
                'description' => $request->description,
                'warehouse_id' => $request->warehouse_id,
                'warehouse_destination_id' => $request->warehouse_destination_id,
                'quantity' =>  count( $request->items ),
            ]);

            foreach ($request->items as $it)
            {
                $inventory = new Inventory();
                $inventory->type = 2;
                $inventory->description = 'Traslado';
                $inventory->item_id = $it['id'];
                $inventory->warehouse_id = $request->warehouse_id;
                $inventory->warehouse_destination_id = $request->warehouse_destination_id;
                $inventory->quantity = $it['quantity'];
                $inventory->inventories_transfer_id = $row->id;

                $inventory->save();

                foreach ($it['lots'] as $lot){

                    if($lot['has_sale']){
                        $item_lot = ItemLot::findOrFail($lot['id']);
                        $item_lot->warehouse_id = $inventory->warehouse_destination_id;
                        $item_lot->update();
                    }

                }
            }

            return  [
                'success' => true,
                'message' => 'Traslado creado con éxito'
            ];
        });

        return $result;


    }


    public function items($warehouse_id)
    {
        return [
            'items' => $this->optionsItemWareHousexId($warehouse_id),
        ];
    }








}
