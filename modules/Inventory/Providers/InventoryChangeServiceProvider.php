<?php

namespace Modules\Inventory\Providers;

use App\Models\Tenant\Item;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Inventory\Models\ItemWarehouse;

class InventoryChangeServiceProvider extends ServiceProvider
{
    use InventoryTrait;

    public function register()
    {
    }

    public function boot()
    {
        $this->createdItem();
        $this->inventory();
    }

    private function createdItem()
    {

        Item::created(function ($item) {


            if($item->unit_type_id == 'ZZ')
            {
                return;
            }
            $warehouse = ($item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($item->warehouse_id)->establishment_id) : $this->findWarehouse();
            if(!$item->is_set){
                $this->createInitialInventory($item->id, $item->stock, $warehouse->id);
            }else{
                $item_warehouse = ItemWarehouse::firstOrNew(['item_id' => $item->id, 'warehouse_id' => $warehouse->id]);
                $item_warehouse->stock = 0;
                $item_warehouse->save();
            }

        });
    }

    private function inventory()
    {
        Inventory::created(function ($inventory) {
            switch ($inventory->type) {
                case 1:
                    $this->createInventoryKardex($inventory, $inventory->item_id, $inventory->quantity, $inventory->warehouse_id);
                    $this->updateStock($inventory->item_id, $inventory->quantity, $inventory->warehouse_id);
                    break;
                case 2:
                    //Origin
                    $this->createInventoryKardex($inventory, $inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);
                    $this->updateStock($inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);
                    //Arrival
                    $this->createInventoryKardex($inventory, $inventory->item_id, $inventory->quantity, $inventory->warehouse_destination_id);
                    $this->updateStock($inventory->item_id, $inventory->quantity, $inventory->warehouse_destination_id);
                    break;
                case 3:
                    $this->createInventoryKardex($inventory, $inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);
                    $this->updateStock($inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);
                    break;
                default:
                    //aqui en el defualt tendria que acceder a la inventory_transactions y determinar el tipo de transaccion
                    //si es ingreso sumo, caso contrario descuento
                    $inventory_transaction = $this->findInventoryTransaction($inventory->inventory_transaction_id);

                    if($inventory_transaction->type === 'input'){

                        $this->createInventoryKardex($inventory, $inventory->item_id, $inventory->quantity, $inventory->warehouse_id);
                        $this->updateStock($inventory->item_id, $inventory->quantity, $inventory->warehouse_id);

                    }else{

                        $this->createInventoryKardex($inventory, $inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);
                        $this->updateStock($inventory->item_id, -1 * $inventory->quantity, $inventory->warehouse_id);

                    }
                    break;
            }
        });
    }

}
