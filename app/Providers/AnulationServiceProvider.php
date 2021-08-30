<?php

namespace App\Providers;

use App\Models\Tenant\Item;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\Purchase;

use App\Models\Tenant\Kardex;
use Illuminate\Support\ServiceProvider;
use App\Traits\KardexTrait;

class AnulationServiceProvider extends ServiceProvider
{
    use KardexTrait;

    public function register()
    {
    }

    public function boot()
    {
        $this->anulation();
        //$this->anulation_purchase();

    }


    private function anulation(){

        Document::updated(function ($document) {

            if($document['document_type_id'] == '01' || $document['document_type_id'] == '03'){

                if($document['state_type_id'] == 11){

                    foreach ($document['items'] as $detail) {

                        // $item = Item::find($detail['item_id']);
                        // $item->stock = $item->stock + $detail['quantity'];
                        // $item->save();

                        $this->updateStock($detail['item_id'], $detail['quantity'], false);

                        $this->saveKardex('sale', $detail['item_id'], $document['id'], -$detail['quantity'],'document');

                    }

                }
            }


        });

    }

    private function anulation_purchase(){

        Purchase::updated(function ($document) {

                if($document['state_type_id'] == 11){

                    foreach ($document['items'] as $detail) {

                        $this->updateStock($detail['item_id'], $detail['quantity'], true); //pongo true porque la compra se anula, entonces el stock disminuye

                       // $this->saveKardex('sale', $detail['item_id'], $document['id'], -$detail['quantity'],'document');

                    }

                }



        });

    }
}
