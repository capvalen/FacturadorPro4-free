<?php

namespace Modules\Inventory\Providers;

use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\Document;
use App\Models\Tenant\Item;
use App\Models\Tenant\PurchaseItem;
use App\Models\Tenant\SaleNoteItem;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Traits\InventoryTrait;
use Modules\Order\Models\OrderNote;
use Modules\Order\Models\OrderNoteItem;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Item\Models\ItemLot;
use Modules\Inventory\Models\DevolutionItem;


class InventoryKardexServiceProvider extends ServiceProvider
{
    use InventoryTrait;

    public function register() {
        //
    }

    public function boot() {
        $this->purchase();
        $this->sale();
        $this->sale_note();
        $this->sale_note_item_delete();
        $this->sale_document_type_03_delete();
        $this->order_note();
        $this->order_note_item_delete();
        $this->purchase_item_delete();
        $this->item_lot_delete();

        $this->devolution();

    }

    private function purchase() {
        PurchaseItem::created(function ($purchase_item) {

            $presentationQuantity = (!empty($purchase_item->item->presentation)) ? $purchase_item->item->presentation->quantity_unit : 1;

            $warehouse = ($purchase_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($purchase_item->warehouse_id)->establishment_id) : $this->findWarehouse();
            // $warehouse = $this->findWarehouse($this->findWarehouseById($purchase_item->warehouse_id)->establishment_id);
            // $warehouse = $this->findWarehouse();
            //$this->createInventory($purchase_item->item_id, $purchase_item->quantity, $warehouse->id);
            $this->createInventoryKardex($purchase_item->purchase, $purchase_item->item_id, /*$purchase_item->quantity*/ ($purchase_item->quantity * $presentationQuantity), $warehouse->id);
            $this->updateStock($purchase_item->item_id, ($purchase_item->quantity * $presentationQuantity), $warehouse->id);
        });
    }

    private function sale() {
        DocumentItem::created(function($document_item) {
            if(!$document_item->item->is_set){

                $presentationQuantity = (!empty($document_item->item->presentation)) ? $document_item->item->presentation->quantity_unit : 1;

                $document = $document_item->document;
                $factor = ($document->document_type_id === '07') ? 1 : -1;

                $warehouse = ($document_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($document_item->warehouse_id)->establishment_id) : $this->findWarehouse();

                //$this->createInventory($document_item->item_id, $factor * $document_item->quantity, $warehouse->id);
                $this->createInventoryKardex($document_item->document, $document_item->item_id, ($factor * ($document_item->quantity * $presentationQuantity)), $warehouse->id);
                if(!$document_item->document->sale_note_id && !$document_item->document->order_note_id) $this->updateStock($document_item->item_id, ($factor * ($document_item->quantity * $presentationQuantity)), $warehouse->id);

            }
            else{

                $item = Item::findOrFail($document_item->item_id);

                foreach ($item->sets as $it) {

                    $ind_item  = $it->individual_item;
                    $item_set_quantity  = ($it->quantity) ? $it->quantity : 1;
                    $presentationQuantity = 1;
                    $document = $document_item->document;
                    $factor = ($document->document_type_id === '07') ? 1 : -1;
                    $warehouse = $this->findWarehouse();
                    $this->createInventoryKardex($document_item->document, $ind_item->id, ($factor * ($document_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);
                    if(!$document_item->document->sale_note_id && !$document_item->document->order_note_id) $this->updateStock($ind_item->id, ($factor * ($document_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);
                }
            }

            /*
             * Calculando el stock por lote por factor según la unidad
             */
            if(isset($document_item->item->IdLoteSelected) )
            {
                if($document_item->item->IdLoteSelected != null)
                {
                    $lot = ItemLotsGroup::query()->find($document_item->item->IdLoteSelected);
                    try {
                        $quantity_unit = $document_item->item->presentation->quantity_unit;
                    }  catch (\Exception $e) {
                        $quantity_unit = 1;
                    }
                    if($document->document_type_id === '07') {
                        $quantity = $lot->quantity + ($quantity_unit * $document_item->quantity);
                    } else {
                        $quantity = $lot->quantity - ($quantity_unit * $document_item->quantity);
                    }
                    $lot->quantity = $quantity;
                    $lot->save();
                }
            }

            if(isset($document_item->item->lots) )
            {
                foreach ($document_item->item->lots as $it) {

                    if($it->has_sale == true)
                    {
                        $r = ItemLot::find($it->id);
                        // $r->has_sale = true;
                        $r->has_sale = ($document->document_type_id === '07') ? false : true;
                        $r->save();
                    }

                }
                /*if($document_item->item->IdLoteSelected != null)
                {
                    $lot = ItemLotsGroup::find($document_item->item->IdLoteSelected);
                    $lot->quantity = ($lot->quantity - $document_item->quantity);
                    $lot->save();
                }*/
            }






        });
    }

    private function sale_note() {
        SaleNoteItem::created(function ($sale_note_item) {

            if(!$sale_note_item->item->is_set){

                $presentationQuantity = (!empty($sale_note_item->item->presentation)) ? $sale_note_item->item->presentation->quantity_unit : 1;

                // $warehouse = $this->findWarehouse($sale_note_item->sale_note->establishment_id);
                $warehouse = ($sale_note_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($sale_note_item->warehouse_id)->establishment_id) : $this->findWarehouse($sale_note_item->sale_note->establishment_id);

                // $this->createInventoryKardex($sale_note_item->sale_note, $sale_note_item->item_id, (-1 * ($sale_note_item->quantity * $presentationQuantity)), $warehouse->id);
                $this->createInventoryKardexSaleNote($sale_note_item->sale_note, $sale_note_item->item_id, (-1 * ($sale_note_item->quantity * $presentationQuantity)), $warehouse->id, $sale_note_item->id);
                if(!$sale_note_item->sale_note->order_note_id) $this->updateStock($sale_note_item->item_id, (-1 * ($sale_note_item->quantity * $presentationQuantity)), $warehouse->id);

            }else{

                $item = Item::findOrFail($sale_note_item->item_id);

                foreach ($item->sets as $it) {

                    $ind_item  = $it->individual_item;
                    $item_set_quantity  = ($it->quantity) ? $it->quantity : 1;
                    $presentationQuantity = 1;
                    $warehouse = $this->findWarehouse($sale_note_item->sale_note->establishment_id);
                    // $this->createInventoryKardex($sale_note_item->sale_note, $ind_item->id , (-1 * ($sale_note_item->quantity * $presentationQuantity)), $warehouse->id);
                    $this->createInventoryKardexSaleNote($sale_note_item->sale_note, $ind_item->id , (-1 * ($sale_note_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id, $sale_note_item->id);
                    if(!$sale_note_item->sale_note->order_note_id) $this->updateStock($ind_item->id , (-1 * ($sale_note_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);

                }

            }

            if(isset($sale_note_item->item->lots) )
            {
                foreach ($sale_note_item->item->lots as $it) {

                    if($it->has_sale == true)
                    {
                        $r = ItemLot::find($it->id);
                        $r->has_sale =  true;
                        $r->save();
                    }

                }
            }
        });
    }



    private function createInventory($item_id, $quantity, $warehouse_id) {
        if(!$this->checkInventory($item_id, $warehouse_id)) {
            $item = $this->findItem($item_id);
            $this->createInitialInventory($item_id, $item->stock + (-1 * $quantity), $warehouse_id);
        }
    }



    private function sale_note_item_delete() {
        SaleNoteItem::deleted(function ($sale_note_item) {

            // dd($sale_note_item);

            if(!$sale_note_item->item->is_set){

                $presentationQuantity = (!empty($sale_note_item->item->presentation)) ? $sale_note_item->item->presentation->quantity_unit : 1;

                // $warehouse = $this->findWarehouse();
                $warehouse = ($sale_note_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($sale_note_item->warehouse_id)->establishment_id) : $this->findWarehouse($sale_note_item->sale_note->establishment_id);

                $this->deleteInventoryKardex($sale_note_item->sale_note, $sale_note_item->inventory_kardex_id);
                $this->updateStock($sale_note_item->item_id, (1 * ($sale_note_item->quantity * $presentationQuantity)), $warehouse->id);

            }else{

                $item = Item::findOrFail($sale_note_item->item_id);

                foreach ($item->sets as $it) {

                    $ind_item  = $it->individual_item;
                    $item_set_quantity  = ($it->quantity) ? $it->quantity : 1;
                    $presentationQuantity = 1;
                    $warehouse = $this->findWarehouse();
                    $this->deleteInventoryKardex($sale_note_item->sale_note, $sale_note_item->inventory_kardex_id);
                    $this->updateStock($ind_item->id , (1 * ($sale_note_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);

                }

            }

            $this->deleteItemLots($sale_note_item);

        });
    }



    private function sale_document_type_03_delete() {

        Document::deleted(function($document) {

            if($document->document_type_id === '03' && $document->state_type_id === '01'){

                foreach ($document->items as $document_item) {


                    if(!$document_item->item->is_set){

                        $presentationQuantity = (!empty($document_item->item->presentation)) ? $document_item->item->presentation->quantity_unit : 1;

                        $factor = 1;
                        $warehouse = $this->findWarehouse();

                        $this->deleteAllInventoryKardexByModel($document);

                        if(!$document->sale_note_id) $this->updateStock($document_item->item_id, ($factor * ($document_item->quantity * $presentationQuantity)), $warehouse->id);

                    }
                    else{

                        $item = Item::findOrFail($document_item->item_id);

                        foreach ($item->sets as $it) {

                            $ind_item  = $it->individual_item;
                            $item_set_quantity  = ($it->quantity) ? $it->quantity : 1;
                            $presentationQuantity = 1;
                            $factor = 1;
                            $warehouse = $this->findWarehouse();

                            $this->deleteAllInventoryKardexByModel($document);
                            if(!$document->sale_note_id) $this->updateStock($ind_item->id, ($factor * ($document_item->quantity * $presentationQuantity * $item_set_quantity)), $warehouse->id);

                        }

                    }

                }
            }


        });
    }



    private function order_note() {

        OrderNoteItem::created(function ($order_note_item) {

            $presentationQuantity = (!empty($order_note_item->item->presentation)) ? $order_note_item->item->presentation->quantity_unit : 1;

            // $warehouse = $this->findWarehouse($order_note_item->order_note->establishment_id);
            $warehouse = ($order_note_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($order_note_item->warehouse_id)->establishment_id) : $this->findWarehouse($order_note_item->order_note->establishment_id);

            $this->createInventoryKardex($order_note_item->order_note, $order_note_item->item_id, (-1 * ($order_note_item->quantity * $presentationQuantity)), $warehouse->id);
            $this->updateStock($order_note_item->item_id, (-1 * ($order_note_item->quantity * $presentationQuantity)), $warehouse->id);

            if(isset($order_note_item->item->lots) )
            {
                foreach ($order_note_item->item->lots as $it) {

                    if($it->has_sale == true)
                    {
                        $r = ItemLot::find($it->id);
                        $r->has_sale = true;
                        $r->save();
                    }

                }
            }

        });
    }


    private function order_note_item_delete() {

        OrderNoteItem::deleted(function ($order_note_item) {



            // dd($order_note_item);
            $presentationQuantity = (!empty($order_note_item->item->presentation)) ? $order_note_item->item->presentation->quantity_unit : 1;

            // $warehouse = $this->findWarehouse($order_note_item->order_note->establishment_id);
            $warehouse = ($order_note_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($order_note_item->warehouse_id)->establishment_id) : $this->findWarehouse($order_note_item->order_note->establishment_id);

            $this->createInventoryKardex($order_note_item->order_note, $order_note_item->item_id , (1 * ($order_note_item->quantity * $presentationQuantity)), $warehouse->id);

            $this->updateStock($order_note_item->item_id, (1 * ($order_note_item->quantity * $presentationQuantity)), $warehouse->id);


        });
    }

    private function purchase_item_delete()
    {
        PurchaseItem::deleted(function ($purchase_item) {


            $presentationQuantity = (!empty($purchase_item->item->presentation)) ? $purchase_item->item->presentation->quantity_unit : 1;

            $warehouse = ($purchase_item->warehouse_id) ? $this->findWarehouse($this->findWarehouseById($purchase_item->warehouse_id)->establishment_id) : $this->findWarehouse();

            $this->verifyHasSaleLots($purchase_item);
            $this->verifyHasSaleLotsGroup($purchase_item);

            $this->deleteItemSeriesAndGroup($purchase_item);

            $this->createInventoryKardex($purchase_item->purchase, $purchase_item->item_id, (-1 * ($purchase_item->quantity * $presentationQuantity)), $warehouse->id);
            // $this->updateStock($purchase_item->item_id, (-1 *($purchase_item->quantity * $presentationQuantity)), $warehouse->id);
            $this->updateStockPurchase($purchase_item->item_id, (-1 *($purchase_item->quantity * $presentationQuantity)), $warehouse->id);

        });
    }
    private function item_lot_delete()
    {
        /*ItemLot::deleted(function($item_lot) {

            if((bool)$item_lot->has_sale)
            {
                throw new Exception("La serie {$item_lot->series} ha sido vendida!");
            }
        });*/
    }



    private function devolution() {

        DevolutionItem::created(function($devolution_item) {

            $devolution = $devolution_item->devolution;

            $warehouse = $this->findWarehouse($devolution_item->devolution->establishment_id);

            //$this->createInventory($devolution_item->item_id, $factor * $devolution_item->quantity, $warehouse->id);
            $this->createInventoryKardex($devolution_item->devolution, $devolution_item->item_id, -$devolution_item->quantity, $warehouse->id);

            $this->updateStock($devolution_item->item_id, -$devolution_item->quantity, $warehouse->id);

            if(isset($devolution_item->item->IdLoteSelected))
            {
                if($devolution_item->item->IdLoteSelected != null)
                {
                    $lot = ItemLotsGroup::find($devolution_item->item->IdLoteSelected);
                    $lot->quantity = $lot->quantity - $devolution_item->quantity;
                    $lot->save();
                }
            }

            if(isset($devolution_item->item->lots) )
            {
                foreach ($devolution_item->item->lots as $it) {

                    if($it->has_sale == true)
                    {
                        $r = ItemLot::find($it->id);
                        $r->has_sale = true;
                        $r->state = 'Inactivo';
                        $r->save();
                    }

                }
            }

        });
    }



}
