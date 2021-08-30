<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Modules\Inventory\Models\Devolution;


class ReportKardexCollection extends ResourceCollection
{

    protected static $balance = 0;
    protected static $restante = 0;
    protected static $re;

    public function toArray($request)
    {
        self::$re = $request;
        $this->calcularRestante(self::$re);

        return $this->collection->transform(function($row, $key) {
            return self::determinateRow($row);
        });
    }

    public static function determinateRow($row){

        $models = [
            "App\Models\Tenant\Document",
            "App\Models\Tenant\Purchase",
            "App\Models\Tenant\SaleNote",
            "Modules\Inventory\Models\Inventory",
            "Modules\Order\Models\OrderNote",
            Devolution::class
        ];

        switch ($row->inventory_kardexable_type) {

            case $models[0]: //venta
                return [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'date_of_issue' => isset($row->inventory_kardexable->date_of_issue) ? $row->inventory_kardexable->date_of_issue->format('Y-m-d') : '',
                    'type_transaction' => ($row->quantity < 0) ? "Venta":"Anulación Venta",
                    'number' => optional($row->inventory_kardexable)->series.'-'.optional($row->inventory_kardexable)->number,
                    'input' => ($row->quantity > 0) ?  (isset($row->inventory_kardexable->sale_note_id)|| isset($row->inventory_kardexable->order_note_id) ? "-" : $row->quantity):"-",
                    // 'input' => ($row->quantity > 0) ?  $row->quantity:"-",
                    'output' => ($row->quantity < 0) ?  (isset($row->inventory_kardexable->sale_note_id)|| isset($row->inventory_kardexable->order_note_id) ? "-" : $row->quantity):"-",
                    'balance' => (isset($row->inventory_kardexable->sale_note_id) || isset($row->inventory_kardexable->order_note_id)) ? self::$balance+=0 : self::$balance+= $row->quantity,
                    'sale_note_asoc' => isset($row->inventory_kardexable->sale_note_id)  ? optional($row->inventory_kardexable)->sale_note->number_full:"-",
                    'order_note_asoc' => isset($row->inventory_kardexable->order_note_id) ? optional($row->inventory_kardexable)->order_note->number_full:"-",
                    // 'sale_note_asoc' => isset($row->inventory_kardexable->sale_note_id)  ? optional($row->inventory_kardexable)->sale_note->prefix.'-'.optional($row->inventory_kardexable)->sale_note->id:"-",
                    'doc_asoc' => isset($row->inventory_kardexable->note) ? $row->inventory_kardexable->note->affected_document->getNumberFullAttribute() : '-'
                ];

            case $models[1]:
                return [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'date_of_issue' => isset($row->inventory_kardexable->date_of_issue) ? $row->inventory_kardexable->date_of_issue->format('Y-m-d') : '',
                    'type_transaction' => ($row->quantity < 0) ? "Anulación Compra":"Compra",
                    'number' => optional($row->inventory_kardexable)->series.'-'.optional($row->inventory_kardexable)->number,
                    'input' => ($row->quantity > 0) ?  $row->quantity:"-",
                    'output' => ($row->quantity < 0) ?  $row->quantity:"-",
                    'balance' => self::$balance+= $row->quantity,
                    'sale_note_asoc' => '-',
                    'order_note_asoc' => '-',
                    'doc_asoc' => '-'
                ];

            case $models[2]: // Nota de venta
                return [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'type_transaction' => "Nota de venta",
                    'date_of_issue' => isset($row->inventory_kardexable->date_of_issue) ? $row->inventory_kardexable->date_of_issue->format('Y-m-d') : '',
                    'number' => optional($row->inventory_kardexable)->number_full,
                    // 'number' => optional($row->inventory_kardexable)->prefix.'-'.optional($row->inventory_kardexable)->id,
                    'input' => ($row->quantity > 0) ?  $row->quantity:"-",
                    'output' => ($row->quantity < 0) ?  $row->quantity:"-",
                    'balance' => self::$balance+= $row->quantity,
                    'sale_note_asoc' => '-',
                    'order_note_asoc' => '-',
                    'doc_asoc' => '-'

                ];

            case $models[3]:{
                $transaction = '';
                $input = '';
                $output = '';

                if(!$row->inventory_kardexable->type){
                    $transaction = InventoryTransaction::findOrFail($row->inventory_kardexable->inventory_transaction_id);
                }

                if($row->inventory_kardexable->type != null){
                    $input = ($row->inventory_kardexable->type == 1) ? $row->quantity : "-";
                }
                else{
                    $input = ($transaction->type == 'input') ? $row->quantity : "-" ;
                }

                if($row->inventory_kardexable->type != null){
                    $output = ($row->inventory_kardexable->type == 2 || $row->inventory_kardexable->type == 3) ? $row->quantity : "-";
                }
                else{
                    $output = ($transaction->type == 'output') ? $row->quantity : "-";
                }
                $user = auth()->user();
                $return = [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'date_of_issue' => '-',
                    'type_transaction' => $row->inventory_kardexable->description,
                    'number' => "-",
                    // 'input' => $input,
                    // 'output' => $output,
                    'balance' => self::$balance+= $row->quantity,
                    'sale_note_asoc' => '-',
                    'order_note_asoc' => '-',
                    'doc_asoc' => '-'
                ];
                if ($row->inventory_kardexable->warehouse_destination_id === $user->establishment_id) {
                    $return['input'] = $output;
                    $return['output'] = $input;
                } else {
                    $return['input'] = $input;
                    $return['output'] = $output;
                }
                return $return;
            }


            case $models[4]:
                return [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'date_of_issue' => isset($row->inventory_kardexable->date_of_issue) ? $row->inventory_kardexable->date_of_issue->format('Y-m-d') : '',
                    'type_transaction' => ($row->quantity < 0) ? "Pedido":"Anulación Pedido",
                    'number' => optional($row->inventory_kardexable)->prefix.'-'.optional($row->inventory_kardexable)->id,
                    'input' => ($row->quantity > 0) ?  $row->quantity:"-",
                    'output' => ($row->quantity < 0) ?  $row->quantity:"-",
                    'balance' => self::$balance+= $row->quantity,
                    'sale_note_asoc' => '-',
                    'order_note_asoc' => '-',
                    'doc_asoc' => '-'
                ];

            case $models[5]: // Devolution
                return [
                    'id' => $row->id,
                    'item_name' => $row->item->description,
                    'date_time' => $row->created_at->format('Y-m-d H:i:s'),
                    'type_transaction' => "Devolución",
                    'date_of_issue' => isset($row->inventory_kardexable->date_of_issue) ? $row->inventory_kardexable->date_of_issue->format('Y-m-d') : '',
                    'number' => optional($row->inventory_kardexable)->number_full,
                    'input' => ($row->quantity > 0) ?  $row->quantity:"-",
                    'output' => ($row->quantity < 0) ?  $row->quantity:"-",
                    'balance' => self::$balance+= $row->quantity,
                    'sale_note_asoc' => '-',
                    'order_note_asoc' => '-',
                    'doc_asoc' => '-'

                ];
        }


    }

    public function calcularRestante($request)
    {

        if($request->page >= 2) {

            $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

            if($request->date_start && $request->date_end) {

                $records = InventoryKardex::where([
                                                ['warehouse_id', $warehouse->id],
                                                ['item_id',$request->item_id],
                                                ['date_of_issue', '<=', $request->date_start]
                                            ])->first();

                $ultimate = InventoryKardex::select(DB::raw('COUNT(*) AS t, MAX(id) AS id'))
                                            ->where([
                                                ['warehouse_id', $warehouse->id],
                                                ['item_id',$request->item_id],
                                                ['date_of_issue', '<=', $request->date_start]
                                            ])->first();


                if (isset($records->date_of_issue) && Carbon::parse($records->date_of_issue)->eq(Carbon::parse($request->date_start))) {

                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        ['date_of_issue', '<=', $request->date_start]
                                                    ])->first();

                    $quantityOld->quantity = 0;

                }elseif($ultimate->t == 1) {

                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        ['date_of_issue', '<=', $request->date_start]
                                                    ])->first();

                } else {

                    $records_previous = InventoryKardex::where([['warehouse_id', $warehouse->id], ['item_id',$request->item_id]])
                                            ->whereBetween('date_of_issue', [$request->date_start, $request->date_end])->take(2)->get();

                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        // ['date_of_issue', '<=', $request->date_start]
                                                        ['date_of_issue', '<', $request->date_start]
                                                    // ])->whereNotIn('id', [$ultimate->id, $previous_ultimate->id])->first();
                                                    ])->whereNotIn('id', $records_previous->pluck('id')->toArray())->first();

                }

                $data = InventoryKardex::with(['inventory_kardexable'])
                                        // ->select('quantity')
                                        ->where([['warehouse_id', $warehouse->id],['item_id',$request->item_id]])
                                        ->whereBetween('date_of_issue', [$request->date_start, $request->date_end])
                                        ->limit(($request->page*20)-20)->get();


                for($i=0;$i<=count($data)-1;$i++) {

                    self::$restante+= (isset($data[$i]->inventory_kardexable->sale_note_id) || isset($data[$i]->inventory_kardexable->order_note_id)) ? 0 : $data[$i]->quantity;
                    // self::$restante += $data[$i]->quantity;
                }

                self::$restante += $quantityOld->quantity;

                self::$balance = self::$restante;

            } else {
                $data = InventoryKardex::where([['warehouse_id', $warehouse->id],['item_id',$request->item_id]])
                    ->limit(($request->page*20)-20)->get();

                for($i=0;$i<=count($data)-1;$i++) {

                    self::$restante+= (isset($data[$i]->inventory_kardexable->sale_note_id) || isset($data[$i]->inventory_kardexable->order_note_id)) ? 0 : $data[$i]->quantity;
                    // self::$restante+=$data[$i]->quantity;
                }
            }

            return self::$balance = self::$restante;

        } else {

            if($request->date_start && $request->date_end) {

                $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

                //primer registro fechas anteriores
                $records = InventoryKardex::where([
                                                ['warehouse_id', $warehouse->id],
                                                ['item_id',$request->item_id],
                                                ['date_of_issue', '<=', $request->date_start]
                                            ])->first();

                $ultimate = InventoryKardex::select(DB::raw('COUNT(*) AS t, MAX(id) AS id'))
                                            ->where([
                                                ['warehouse_id', $warehouse->id],
                                                ['item_id',$request->item_id],
                                                ['date_of_issue', '<=', $request->date_start]
                                            ])->first();


                if (isset($records->date_of_issue) && Carbon::parse($records->date_of_issue)->eq(Carbon::parse($request->date_start))) {

                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        ['date_of_issue', '<=', $request->date_start]
                                                    ])->first();

                    $quantityOld->quantity = 0;

                }elseif($ultimate->t == 1) {

                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        ['date_of_issue', '<=', $request->date_start]
                                                    ])->first();
                } else {

                    $records_previous = InventoryKardex::where([['warehouse_id', $warehouse->id], ['item_id',$request->item_id]])
                                                    ->whereBetween('date_of_issue', [$request->date_start, $request->date_end])->take(2)->get();


                    $quantityOld = InventoryKardex::select(DB::raw('SUM(quantity) AS quantity'))
                                                    ->where([
                                                        ['warehouse_id', $warehouse->id],
                                                        ['item_id',$request->item_id],
                                                        // ['date_of_issue', '<=', $request->date_start]
                                                        ['date_of_issue', '<', $request->date_start]
                                                    ])->whereNotIn('id', $records_previous->pluck('id')->toArray())->first();


                }

                return self::$balance = $quantityOld->quantity;
            }

        }

    }
}
