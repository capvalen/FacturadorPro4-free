<?php

namespace Modules\Dashboard\Helpers;

use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\PurchaseItem;
use App\Models\Tenant\SaleNoteItem;
use Carbon\Carbon;
use Modules\Expense\Models\Expense;

class DashboardUtility
{
    public function data($request)
    {

        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $enabled_expense = $request['enabled_expense'];
        $item_id = $request['item_id'];

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
            'utilities' => $this->utilities_totals($establishment_id, $d_start, $d_end, $enabled_expense, $item_id),
        ];

    }



    private function utilities_totals($establishment_id, $d_start, $d_end, $enabled_expense, $item_id){


        if($d_start && $d_end){

            $document_items = DocumentItem::without(['affectation_igv_type', 'system_isc_type', 'price_type'])
                                            ->whereHas('document',function($query) use($establishment_id, $d_start, $d_end){
                                                $query->where('establishment_id', $establishment_id)
                                                        ->whereIn('state_type_id', ['01','03','05','07','13'])
                                                        ->whereBetween('date_of_issue', [$d_start, $d_end])
                                                        ->whereIn('document_type_id', ['01','03','08']);
                                            })
                                            ->get();


            $sale_note_items = SaleNoteItem::without(['affectation_igv_type', 'system_isc_type', 'price_type'])
                                            ->whereHas('sale_note', function($query) use($establishment_id, $d_start, $d_end){

                                                $query->where([['establishment_id', $establishment_id],['changed',false]])
                                                        ->whereIn('state_type_id', ['01','03','05','07','13'])
                                                        ->whereBetween('date_of_issue', [$d_start, $d_end]);
                                            })
                                            ->get();

            $expenses = ($enabled_expense) ? Expense::where('establishment_id', $establishment_id)->whereBetween('date_of_issue', [$d_start, $d_end])->get():null;


        }else{
            $document_items = DocumentItem::without(['affectation_igv_type', 'system_isc_type', 'price_type'])
                                            ->whereHas('document', function($query) use($establishment_id) {
                                                $query->where('establishment_id', $establishment_id)
                                                        ->whereIn('state_type_id', ['01','03','05','07','13']);
                                            })
                                            ->get();


            $sale_note_items = SaleNoteItem::without(['affectation_igv_type', 'system_isc_type', 'price_type'])
                                            ->whereHas('sale_note', function($query) use($establishment_id){

                                                $query->where([['establishment_id', $establishment_id],['changed',false]])
                                                        ->whereIn('state_type_id', ['01','03','05','07','13']);
                                            })
                                            ->get();


            $expenses = ($enabled_expense) ? Expense::where('establishment_id', $establishment_id)->get():null;

        }

        if($item_id){
            $document_items = $document_items->where('item_id', $item_id);
            $sale_note_items = $sale_note_items->where('item_id', $item_id);
        }

        // dd($document_items);
        $getTotalDocumentItems = $this->getTotalDocumentItems($document_items);
        $getTotalSaleNoteItems = $this->getTotalSaleNoteItems($sale_note_items);
        $getTotalExpenses = $this->getTotalExpenses($expenses);

        // dd($getTotalDocumentItems, $getTotalSaleNoteItems, $getTotalExpenses);

        $total_income = $getTotalDocumentItems['document_sale_total'] + $getTotalSaleNoteItems['sale_note_sale_total'];
        $total_egress = $getTotalDocumentItems['document_purchase_total'] + $getTotalSaleNoteItems['sale_note_purchase_total'] + $getTotalExpenses;
        $utility = $total_income - $total_egress;


        return [
            'totals' => [
                'total_income' => number_format($total_income,2, ".", ""),
                'total_egress' => number_format($total_egress,2, ".", ""),
                'utility' => number_format($utility,2, ".", ""),
            ],
            'graph' => [
                'labels' => ['Ingreso', 'Egreso'],
                'datasets' => [
                    [
                        'label' => 'Utilidades',
                        'data' => [round($total_income,2), round($total_egress,2)],
                        'backgroundColor' => [
                            'rgb(54, 162, 235)',
                            'rgb(255, 99, 132)',
                        ]
                    ]
                ],
            ]
        ];

    }


    private function getTotalExpenses($expenses){

        if($expenses){

            $total = 0;
            foreach ($expenses as $ex) {
                $total += ($ex->currency_type_id == 'USD') ? $ex->total * $ex->exchange_rate_sale: $ex->total;
            }

            return number_format($total, 2, ".", "");
        }

        return 0;
    }


    private function getPurchaseUnitPrice($record){

        $purchase_unit_price = 0;

        if($record->item->unit_type_id != 'ZZ'){

            if($record->relation_item->purchase_unit_price > 0){

                $purchase_unit_price = $record->relation_item->purchase_unit_price;

            }else{

                $purchase_item = PurchaseItem::select('unit_price')->where('item_id', $record->item_id)->latest('id')->first();
                $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : $record->unit_price;

            }

        }

        return $purchase_unit_price;
    }

    private function getTotalSaleNoteItems($sale_note_items){

        $purchase_unit_price = 0;

        //PEN
        $sale_note_sale_total_pen = 0;
        $sale_note_purchase_total_pen = 0;
        $sale_note_utility_total_pen = 0;

        //USD
        $sale_note_sale_total_usd = 0;
        $sale_note_purchase_total_usd = 0;
        $sale_note_utility_total_usd = 0;

        foreach ($sale_note_items as $sln) {

            $purchase_unit_price = $this->getPurchaseUnitPrice($sln);

            $sln_total_purchase = $purchase_unit_price * $sln->quantity;

            if($sln->sale_note->currency_type_id === 'PEN'){

                    $sale_note_purchase_total_pen += $sln_total_purchase;
                    $sale_note_sale_total_pen += $sln->total;

            }else{

                $sale_note_purchase_total_usd += $sln_total_purchase;
                $sale_note_sale_total_usd += $sln->total * $sln->sale_note->exchange_rate_sale;

            }

        }

        $sale_note_utility_total_pen = $sale_note_sale_total_pen - $sale_note_purchase_total_pen;
        $sale_note_utility_total_usd = $sale_note_sale_total_usd - $sale_note_purchase_total_usd;


        return [

            // 'sale_note_sale_total_pen' => round($sale_note_sale_total_pen, 2),
            // 'sale_note_purchase_total_pen' => round($sale_note_purchase_total_pen, 2),

            // 'sale_note_purchase_total_usd' => round($sale_note_purchase_total_usd, 2),
            // 'sale_note_sale_total_usd' => round($sale_note_sale_total_usd, 2),

            // 'sale_note_utility_total_pen' => round($sale_note_utility_total_pen, 2),
            // 'sale_note_utility_total_usd' => round($sale_note_utility_total_usd, 2),

            'sale_note_sale_total' => $sale_note_sale_total_usd + $sale_note_sale_total_pen,
            'sale_note_purchase_total' => $sale_note_purchase_total_usd + $sale_note_purchase_total_pen,

        ];
    }

    private function getTotalDocumentItems($document_items) {
        $purchase_unit_price = 0;
        $purchase_currency_type = null;

        //PEN
        $document_total_note_credit_pen = 0;

        $document_sale_total_pen = 0;
        $document_purchase_total_pen = 0;
        $document_utility_total_pen = 0;

        //USD
        $document_total_note_credit_usd = 0;
        $document_sale_total_usd = 0;
        $document_purchase_total_usd = 0;
        $document_utility_total_usd = 0;

        $documentsIds = $document_items->pluck('document_id')->all();
        // Obteniendo todos los documentos de los items q recibe la función
        $documents = Document::without(['user', 'soap_type', 'state_type', 'document_type', 'currency_type', 'group', 'items', 'invoice', 'note', 'payments'])
            ->whereIn('id', $documentsIds)
            ->select('id', 'total', 'document_type_id', 'currency_type_id')
            ->get();

        foreach ($documents as $doc) {
            if($doc->currency_type_id === 'PEN'){
                if(in_array($doc->document_type_id,['01','03','08'])){
                    $document_sale_total_pen += $doc->total;
                }else{
                    $document_sale_total_pen -= $doc->total;
                }
            } else {
                if(in_array($doc->document_type_id,['01','03','08'])){
                    $document_sale_total_usd += $doc->total * $doc->exchange_rate_sale;
                }else{
                    $document_sale_total_usd -= $doc->total * $doc->exchange_rate_sale;
                }
            }
        }

        foreach ($document_items as $doc_it) {
            $purchase_unit_price = $this->getPurchaseUnitPrice($doc_it);


            $doc_total_purchase = $purchase_unit_price * $doc_it->quantity;

            if($doc_it->document->currency_type_id === 'PEN'){
                if(in_array($doc_it->document->document_type_id,['01','03','08'])){
                    $document_purchase_total_pen += $doc_total_purchase;
                    // $document_sale_total_pen += $doc_it->total;
                }else{
                    $document_purchase_total_pen -= $doc_total_purchase;
                    // $document_sale_total_pen -= $doc_it->total;
                }
            } else {
                if(in_array($doc_it->document->document_type_id,['01','03','08'])){
                    $document_purchase_total_usd += $doc_total_purchase;
                    // $document_sale_total_usd += $doc_it->total * $doc_it->document->exchange_rate_sale;
                }else{

                    $document_purchase_total_usd -= $doc_total_purchase;
                    // $document_sale_total_usd -= $doc_it->total * $doc_it->document->exchange_rate_sale;
                }
            }
        }

        $document_utility_total_pen = $document_sale_total_pen - $document_purchase_total_pen;
        $document_utility_total_usd = $document_sale_total_usd - $document_purchase_total_usd;

        return [

            'document_sale_total_pen' => round($document_sale_total_pen, 2),
            'document_purchase_total_pen' => round($document_purchase_total_pen, 2),

            'document_purchase_total_usd' => round($document_purchase_total_usd, 2),
            'document_sale_total_usd' => round($document_sale_total_usd, 2),

            'document_utility_total_pen' => round($document_utility_total_pen, 2),
            'document_utility_total_usd' => round($document_utility_total_usd, 2),

            'document_sale_total' => $document_sale_total_usd + $document_sale_total_pen,
            'document_purchase_total' => $document_purchase_total_usd + $document_purchase_total_pen,

        ];
    }


}
