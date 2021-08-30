<?php

namespace Modules\Dashboard\Traits;

use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use Carbon\Carbon;
use App\Models\Tenant\Person;
use App\Models\Tenant\Purchase;
use Modules\Expense\Models\Expense;

trait TotalsTrait
{

    public function get_purchase_totals($establishment_id, $date_start, $date_end)
    {

        if($date_start && $date_end){

            $purchases = Purchase::query()->whereIn('state_type_id', ['01','03','05','07','13'])
                                        ->where('establishment_id', $establishment_id)
                                        ->whereBetween('date_of_issue', [$date_start, $date_end])
                                        ->get();

        }else{

            $purchases = Purchase::query()->whereIn('state_type_id', ['01','03','05','07','13'])
                                        ->where('establishment_id', $establishment_id)
                                        ->get();
        }


        $purchases_total = $purchases->where('currency_type_id', 'PEN')->sum('total') + $purchases->where('currency_type_id', 'PEN')->sum('total_perception');
        $purchases_total_usd = 0;


        $purchase_total_payment = 0;
        $purchase_total_payment_usd = 0;


        foreach ($purchases as $purchase)
        {

            if($purchase->currency_type_id == 'PEN'){

                $purchase_total_payment += collect($purchase->purchase_payments)->sum('payment');

            }else{
                $purchases_total_usd += $purchase->total * $purchase->exchange_rate_sale +  $purchases->sum('total_perception');;
                $purchase_total_payment_usd += collect($purchase->purchase_payments)->sum('payment') * $purchase->exchange_rate_sale;
            }
        }

        $total = $purchases_total + $purchases_total_usd;
        $total_payment = $purchase_total_payment +$purchase_total_payment_usd;

        return [
            'totals' => [
                'total_payment' => round($total_payment,2),
                'total' => round($total,2),
            ]
        ];
    }


    public function get_expense_totals($establishment_id, $date_start, $date_end)
    {


        if($date_start && $date_end){

            $expenses = Expense::query()->where('establishment_id', $establishment_id)
                                        ->whereBetween('date_of_issue', [$date_start, $date_end])
                                        ->where('state_type_id','05')
                                        ->get();

        }else{

            $expenses = Expense::query()->where('establishment_id', $establishment_id)
                                        ->where('state_type_id','05')
                                        ->get();
        }

        $expenses_total = $expenses->where('currency_type_id', 'PEN')->sum('total');

        $expense_dolla = $expenses->where('currency_type_id', 'USD');

        foreach ($expense_dolla as $exp) {
            $expenses_total += $exp->total * $exp->exchange_rate_sale;
        }

        $expense_total_payment = 0;

        foreach ($expenses as $expense)
        {
            $expense_total_payment += collect($expense->payments)->sum('payment');
        }

        return [
            'totals' => [
                'total_payment' => round($expense_total_payment,2),
                'total' => round($expenses_total,2),
            ]
        ];

    }


    public function get_sale_note_totals($establishment_id, $date_start, $date_end)
    {

        if($date_start && $date_end){
            $sale_notes = SaleNote::query()->whereStateTypeAccepted()
                                           ->where('establishment_id', $establishment_id)
                                           ->where('changed', false)
                                           ->whereBetween('date_of_issue', [$date_start, $date_end])->get();
        }else{
            $sale_notes = SaleNote::query()->whereStateTypeAccepted()
                                           ->where('establishment_id', $establishment_id)
                                           ->where('changed', false)->get();
        }


        //PEN
        $sale_note_total_pen = 0;
        $sale_note_total_payment_pen = 0;

        $sale_note_total_pen = collect($sale_notes->where('currency_type_id', 'PEN'))->sum('total');

        //USD
        $sale_note_total_usd = 0;
        $sale_note_total_payment_usd = 0;

        //TWO CURRENCY
        foreach ($sale_notes as $sale_note)
        {

            if($sale_note->currency_type_id == 'PEN'){

                $sale_note_total_payment_pen += collect($sale_note->payments)->sum('payment');

            }else{

                $sale_note_total_usd += $sale_note->total * $sale_note->exchange_rate_sale;
                $sale_note_total_payment_usd += collect($sale_note->payments)->sum('payment') * $sale_note->exchange_rate_sale;

            }
        }

        //TOTALS
        $sale_note_total = $sale_note_total_pen + $sale_note_total_usd;
        $sale_note_total_payment = $sale_note_total_payment_pen + $sale_note_total_payment_usd;


        return [
            'totals' => [
                'total_payment' => round($sale_note_total_payment,2),
                'total' => round($sale_note_total,2),
            ]
        ];
    }


    public function get_document_totals($establishment_id, $date_start, $date_end)
    {

        if($date_start && $date_end){
            $documents = Document::query()->where('establishment_id', $establishment_id)->whereBetween('date_of_issue', [$date_start, $date_end])->get();
        }else{
            $documents = Document::query()->where('establishment_id', $establishment_id)->get();
        }

        //PEN
        $document_total_pen = 0;
        $document_total_payment_pen = 0;
        $document_total_note_credit_pen = 0;

        $document_total_pen = collect($documents->whereIn('state_type_id', ['01','03','05','07','13'])->whereIn('document_type_id', ['01','03','08']))->where('currency_type_id', 'PEN')->sum('total');

        //USD
        $document_total_usd = 0;
        $document_total_note_credit_usd = 0;
        $document_total_payment_usd = 0;

        $documents_usd = $documents->whereIn('state_type_id', ['01','03','05','07','13'])
                                    ->whereIn('document_type_id', ['01','03','08'])
                                    ->where('currency_type_id', 'USD');

        foreach ($documents_usd as $dusd) {
            $document_total_usd += $dusd->total * $dusd->exchange_rate_sale;
        }

        //TWO CURRENCY

        foreach ($documents as $document)
        {
            if($document->currency_type_id == 'PEN'){

                if(in_array($document->state_type_id,['01','03','05','07','13'])){

                    $document_total_payment_pen += collect($document->payments)->sum('payment');
                    $document_total_note_credit_pen += ($document->document_type_id == '07') ? $document->total:0; //nota de credito

                }


            }else{

                if(in_array($document->state_type_id,['01','03','05','07','13'])){
                    
                    $document_total_payment_usd += collect($document->payments)->sum('payment') * $document->exchange_rate_sale;
                    $document_total_note_credit_usd += ($document->document_type_id == '07') ? $document->total * $document->exchange_rate_sale:0; //nota de credito
                
                }

            }

        }

        //TOTALS
        $document_total = $document_total_pen + $document_total_usd;
        $document_total_note_credit = $document_total_note_credit_pen + $document_total_note_credit_usd;
        $document_total_payment = $document_total_payment_pen + $document_total_payment_usd;

        $document_total = round(($document_total - $document_total_note_credit),2);

        return [
            'totals' => [
                'total_payment' => round($document_total_payment,2),
                'total' => round($document_total,2),
            ]
        ];
    }

}
