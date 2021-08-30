<?php

namespace Modules\Dashboard\Helpers;

use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\Person;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Item;
use Carbon\Carbon;

class DashboardSalePurchase
{
    public function data($request)
    {
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $enabled_move_item = $request['enabled_move_item'];
        $enabled_transaction_customer = $request['enabled_transaction_customer'];

        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
            case 'last_week':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        return [
            'purchase' => $this->purchase_totals($establishment_id, $d_start, $d_end),
            'items_by_sales' => $this->items_by_sales($establishment_id, $d_start, $d_end, $enabled_move_item),
            'top_customers' => $this->top_customers($establishment_id, $d_start, $d_end, $enabled_transaction_customer),
        ];
    }

    private function top_customers($establishment_id, $d_start, $d_end, $enabled_transaction_customer){

        // $documents = Document::get();
        // $sale_notes = SaleNote::get();
        if($d_start && $d_end){

            $documents = Document::query()->where('establishment_id', $establishment_id)
                                    ->whereIn('state_type_id', ['01','03','05','07','13'])
                                    ->whereBetween('date_of_issue', [$d_start, $d_end])->get();


            $sale_notes = SaleNote::query()->where([['establishment_id', $establishment_id],['changed',false]])
                                    ->whereIn('state_type_id', ['01','03','05','07','13'])
                                    ->whereBetween('date_of_issue', [$d_start, $d_end])->get();
        }else{

            $documents = Document::query()->where('establishment_id', $establishment_id)
                    ->whereIn('state_type_id', ['01','03','05','07','13'])->get();


            $sale_notes = SaleNote::query()->where([['establishment_id', $establishment_id],['changed',false]])
                    ->whereIn('state_type_id', ['01','03','05','07','13'])->get();

        }

        foreach ($sale_notes as $sn) {
            $documents->push($sn);
        }

        $all_records = $documents;

        $group_customers = $all_records->groupBy('customer_id');

        $top_customers = collect([]);

        foreach ($group_customers as $customers) {

            // $customers es un cliente con todos sus documentos generados
            // dd($customers[0]->total);

            $transaction_quantity_sale = $customers->whereIn('document_type_id', ['01','03','08'])->count() + $customers->where('prefix', 'NV')->count();
            $transaction_quantity_credit_note =$customers->where('document_type_id', '07')->count();

            $transaction_quantity = $transaction_quantity_sale - $transaction_quantity_credit_note;

            $customer = Person::where('type','customers')->find($customers[0]->customer_id);

            $totals = $customers->whereIn('document_type_id', ['01','03','08'])->sum(function ($row) {
                return $this->calculateTotalCurrency($row->currency_type_id, $row->exchange_rate_sale, $row->total);//count($product['colors']);
            });    //('total');


            //totales en documents
            $totals_sale_note = $customers->where('prefix', 'NV')->sum(function ($row) {
                return $this->calculateTotalCurrency($row->currency_type_id, $row->exchange_rate_sale, $row->total);//count($product['colors']);
            });


            $total_credit_note = $customers->where('document_type_id','07')->sum('total');

            $difference = ($totals + $totals_sale_note) - $total_credit_note;

            if($difference > 0)
                $top_customers->push([
                    'total' => number_format($difference,2, ".", ""),
                    'name' => $customer->name,
                    'number' => $customer->number,
                    'transaction_quantity' => $transaction_quantity,
                ]);

        }

        $order_column = ($enabled_transaction_customer) ? 'transaction_quantity' : 'total';
        $sorted = $top_customers->sortByDesc($order_column);

        return $sorted->values()->take(10);

    }



    private function purchase_totals($establishment_id, $d_start, $d_end)
    {
        // $purchases = Purchase::get();
        $purchases = Purchase::without(['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'purchase_payments'])
            ->whereIn('state_type_id', ['01','03','05','07','13'])
            ->where('establishment_id', $establishment_id)
            ->select('id', 'state_type_id', 'establishment_id', 'currency_type_id', 'total', 'exchange_rate_sale', 'total_perception')
            ->get();

        $purchases_total = $purchases->where('currency_type_id', 'PEN')->sum('total');

        $purchase_dollr = $purchases->where('currency_type_id', 'USD');

        foreach ($purchase_dollr as $pr) {
            $purchases_total +=  $pr->total * $pr->exchange_rate_sale;
        }
        $purchases_total_perception = round($purchases->sum('total_perception'),2);


        $data_array = ['Ene', 'Feb','Mar', 'Abr','May', 'Jun','Jul', 'Ago','Sep', 'Oct', 'Nov', 'Dic'];

        $purchases_by_month = $purchases->groupBy(function($date) {
                                return Carbon::parse($date->date_of_issue)->format('m');
                            });


        return [
            'totals' => [
                'purchases_total_perception' => number_format($purchases_total_perception,2),
                'purchases_total' => number_format( round($purchases_total, 2),2),
                'total' => number_format($purchases_total + $purchases_total_perception,2),
            ],
            'graph' => [
                'labels' => $data_array,
                'datasets' => [
                    [
                        'label' => 'Total percepciones',
                        'data' => $this->arrayPurchasesbyMonth($purchases_by_month, 'total_perception'),
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor' => 'rgb(255, 99, 132)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ],
                    [
                        'label' => 'Total compras',
                        'data' => $this->arrayPurchasesbyMonth($purchases_by_month, 'total'),
                        'backgroundColor' => 'rgb(54, 162, 235)',
                        'borderColor' => 'rgb(54, 162, 235)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ],
                    [
                        'label' => 'Total',
                        'data' => $data_array,
                        'backgroundColor' => 'rgb(201, 203, 207)',
                        'borderColor' => 'rgb(201, 203, 207)',
                        'borderWidth' => 1,
                        'fill' => false,
                        'lineTension' => 0,
                    ]

                ],
            ]
        ];
    }



    private function items_by_sales($establishment_id, $d_start, $d_end, $enabled_move_item) {
        if ($d_start && $d_end) {

            $documents = Document::without(['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'invoice', 'note', 'payments'])
                        ->where('establishment_id', $establishment_id)
                        ->whereIn('state_type_id', ['01','03','05','07','13'])
                        ->whereBetween('date_of_issue', [$d_start, $d_end])->get();


            $sale_notes = SaleNote::without(['user', 'soap_type', 'state_type', 'currency_type', 'items', 'payments'])
                        ->where([['establishment_id', $establishment_id],['changed',false]])
                        ->whereIn('state_type_id', ['01','03','05','07','13'])
                        ->whereBetween('date_of_issue', [$d_start, $d_end])->get();
        } else {

            $documents = Document::without(['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'invoice', 'note', 'payments'])
                        ->where('establishment_id', $establishment_id)
                        ->whereIn('state_type_id', ['01','03','05','07','13'])->get();


            $sale_notes = SaleNote::without(['user', 'soap_type', 'state_type', 'currency_type', 'items', 'payments'])
                        ->where([['establishment_id', $establishment_id],['changed',false]])
                        ->whereIn('state_type_id', ['01','03','05','07','13'])->get();

        }

        $document_items = collect([]);
        $sale_note_items = collect([]);

        foreach ($documents as $doc) {
            foreach ($doc->items as $item) {
                $document_items->push($item);
            }
        }

        foreach ($sale_notes as $s_notes) {
            foreach ($s_notes->items as $item) {
                $sale_note_items->push($item);
            }
        }


        foreach ($sale_note_items as $sni) {
            $document_items->push($sni);
        }

        $all_items = $document_items;
        $group_items = $all_items->groupBy('item_id');

        $items_by_sales = collect([]);

        foreach ($group_items as $items) {
            $item = Item::without(['item_type', 'unit_type', 'currency_type', 'warehouses','item_unit_types', 'tags'])
                ->where('status',true)->find($items[0]->item_id);

            $totals = 0;
            $total_credit_note = 0;
            $move_quantity = 0;

            foreach ($items as $it) {

                if($it->document){

                    if(in_array($it->document->document_type_id,['01','03','08'])){


                        $totals += $this->calculateTotalCurrency($it->document->currency_type_id, $it->document->exchange_rate_sale, $it->document->total);
                        $move_quantity += $it->quantity;

                    }else{

                        $total_credit_note += $this->calculateTotalCurrency($it->document->currency_type_id, $it->document->exchange_rate_sale, $it->document->total);
                        $move_quantity -= $it->quantity;

                    }

                }else{

                    $totals += $this->calculateTotalCurrency($it->sale_note->currency_type_id, $it->sale_note->exchange_rate_sale, $it->sale_note->total);
                    $move_quantity += $it->quantity;

                }

            }

            $difference = $totals - $total_credit_note;

            if($item && $difference > 0){
                $items_by_sales->push([
                    'total' => number_format($difference, 2, ".", ""),
                    'description' => $item->description,
                    'internal_id' => $item->internal_id,
                    'move_quantity' => number_format($move_quantity, 2, ".", ""),
                ]);
            }
        }

        $order_column = ($enabled_move_item) ? 'move_quantity' : 'total';
        $sorted = $items_by_sales->sortByDesc($order_column);

        return $sorted->values()->take(10);

    }

    /**
     * @param $purchases
     * @return array
     */
    private function arrayPurchasesbyMonth($purchases_by_month, $total){

        return [
            isset($purchases_by_month['01']) ? round($purchases_by_month['01']->sum($total), 2) : 0,
            isset($purchases_by_month['02']) ? round($purchases_by_month['02']->sum($total), 2) : 0,
            isset($purchases_by_month['03']) ? round($purchases_by_month['03']->sum($total), 2) : 0,
            isset($purchases_by_month['04']) ? round($purchases_by_month['04']->sum($total), 2) : 0,
            isset($purchases_by_month['05']) ? round($purchases_by_month['05']->sum($total), 2) : 0,
            isset($purchases_by_month['06']) ? round($purchases_by_month['06']->sum($total), 2) : 0,
            isset($purchases_by_month['07']) ? round($purchases_by_month['07']->sum($total), 2) : 0,
            isset($purchases_by_month['08']) ? round($purchases_by_month['08']->sum($total), 2) : 0,
            isset($purchases_by_month['09']) ? round($purchases_by_month['09']->sum($total), 2) : 0,
            isset($purchases_by_month['10']) ? round($purchases_by_month['10']->sum($total), 2) : 0,
            isset($purchases_by_month['11']) ? round($purchases_by_month['11']->sum($total), 2) : 0,
            isset($purchases_by_month['12']) ? round($purchases_by_month['12']->sum($total), 2) : 0
        ];

    }

    private function calculateTotalCurrency($currency_type_id, $exchange_rate_sale,  $total)
    {
        if($currency_type_id == 'USD')
        {
            return $total * $exchange_rate_sale;
        }
        else{
            return $total;
        }
    }




}
