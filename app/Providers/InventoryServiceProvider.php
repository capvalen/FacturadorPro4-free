<?php

namespace App\Providers;
 
use App\Models\Tenant\Inventory; 
use App\Models\Tenant\Item; 
use Illuminate\Support\ServiceProvider;
use App\Traits\InventoryKardexTrait; 

class InventoryServiceProvider extends ServiceProvider
{

    use InventoryKardexTrait;
    
    public function register()
    {
    }

     
    public function boot()
    {
        //  $this->saveItem();
        //  $this->inventory();
    }


    private function inventory()
    {

        Inventory::created(function ($inventory) {
 
            switch ($inventory->type) {
                case 1:
                    $inventory_kardex_initial = $this->saveInventoryKardex($inventory, $inventory->item_id, null, $inventory->quantity,$inventory->warehouse_id);  
                    $this->saveItemWarehouse($inventory->item_id, null, $inventory_kardex_initial->quantity,$inventory->warehouse_id);

                    break;
                case 2:

                    //partida
                    $inventory_kardex_exit = $this->saveInventoryKardex($inventory, $inventory->item_id, null, -$inventory->quantity,$inventory->warehouse_id);               
                    $this->updateStock($inventory->item_id,null, -$inventory_kardex_exit->quantity, true, $inventory->warehouse_id);


                    //destino
                    $inventory_kardex_entry = $this->saveInventoryKardex($inventory, $inventory->item_id, null, $inventory->quantity,$inventory->warehouse_destination_id);               
                    

                    if($this->getItemWarehouse($inventory->item_id, null, $inventory->warehouse_destination_id)){

                        $this->updateStock($inventory->item_id,null, $inventory_kardex_entry->quantity, false, $inventory->warehouse_destination_id);

                    }else{
                        $this->saveItemWarehouse($inventory->item_id, null, $inventory_kardex_entry->quantity,$inventory->warehouse_destination_id);
                    }
                
                    break;

                case 3:

                    $inventory_kardex_remove = $this->saveInventoryKardex($inventory, $inventory->item_id, null, -$inventory->quantity,$inventory->warehouse_id);        
                    $this->updateStock($inventory->item_id,null, -$inventory_kardex_remove->quantity, true, $inventory->warehouse_id);
                
                    break;
                 
            }
                
           
        });
    }

    private function saveItem(){

        Item::created(function ($item) { 
 
            $this->saveInventory($item);
            
        });

    }

    
    private function saveInventory($item){

        Inventory::create([
            'type' => 1,
            'description' => 'Stock inicial',
            'item_id' => $item->id,
            'warehouse_id' => $item->warehouse_id,
            'quantity' => ($item->stock) ? $item->stock : 0
        ]);

    }

}
