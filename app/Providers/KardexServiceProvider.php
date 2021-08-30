<?php

namespace App\Providers;

use App\Models\Tenant\Item;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\Document;
use App\Models\Tenant\PurchaseItem;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\Kardex;
use Illuminate\Support\ServiceProvider;
use App\Traits\KardexTrait;

class KardexServiceProvider extends ServiceProvider
{
    use KardexTrait;

    public function boot()
    {
        $this->save_item();
        $this->sale();
        $this->purchase();
        $this->sale_note();

    }

    public function register()
    {

    }

    private function sale()
    {
        DocumentItem::created(function ($document_item) {
            $document = Document::whereIn('document_type_id',['01','03'])->find($document_item->document_id);
            if($document){

                $kardex = $this->saveKardex('sale', $document_item->item_id, $document_item->document_id, $document_item->quantity, 'document');

                if($document->state_type_id != 11){

                    $this->updateStock($document_item->item_id, $kardex->quantity, true);

                }

            }
        });
    }

    private function purchase()
    {
        PurchaseItem::created(function ($purchase_item) {

            $kardex = $this->saveKardex('purchase', $purchase_item->item_id, $purchase_item->purchase_id, $purchase_item->quantity, 'purchase');

            $this->updateStock($purchase_item->item_id, $kardex->quantity, false);

        });
    }

    private function sale_note()
    {
        SaleNoteItem::created(function ($sale_note_item) {

            $kardex = $this->saveKardex('sale', $sale_note_item->item_id, $sale_note_item->sale_note_id, $sale_note_item->quantity, 'sale_note');

            $this->updateStock($sale_note_item->item_id, $kardex->quantity, true);

        });
    }

    private function save_item(){

        Item::created(function ($item) {

            $stock = ($item->stock) ? $item->stock : 0;
            $kardex = $this->saveKardex(null, $item->id, null, $stock, null);

        });

    }



}
