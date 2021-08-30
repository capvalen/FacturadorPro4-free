<?php

namespace Modules\Finance\Traits; 

use App\Models\Tenant\Establishment;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNotePayment;
use App\Models\Tenant\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait UnpaidTrait
{ 

    public function transformRecords($records) {

        return $records->transform(function($row, $key) {

            $total_to_pay = (float)$row->total - (float)$row->total_payment;
            $delay_payment = null;
            $date_of_due = null;

            if($total_to_pay > 0) {
                if($row->document_type_id){

                    $invoice = Invoice::where('document_id', $row->id)->first();
                    if($invoice)
                    {
                        $due =   Carbon::parse($invoice->date_of_due); // $invoice->date_of_due;
                        $date_of_due = $invoice->date_of_due->format('Y/m/d');
                        $now = Carbon::now();

                        if($now > $due){

                            $delay_payment = $now->diffInDays($due);
                        }


                    }
                }
            }

            $guides = null;
            $date_payment_last = '';

            if($row->document_type_id){
                $guides =  Dispatch::where('reference_document_id', $row->id )->orderBy('series')->orderBy('number', 'desc')->get()->transform(function($item) {
                    return [
                        'id' => $item->id,
                        'external_id' => $item->external_id,
                        'number' => $item->number_full,
                        'date_of_issue' => $item->date_of_issue->format('Y-m-d'),
                        'date_of_shipping' => $item->date_of_shipping->format('Y-m-d'),
                        'download_external_xml' => $item->download_external_xml,
                        'download_external_pdf' => $item->download_external_pdf,
                    ];
                });

                $date_payment_last = DocumentPayment::where('document_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
            }
            else{
                $date_payment_last = SaleNotePayment::where('sale_note_id', $row->id)->orderBy('date_of_payment', 'desc')->first();
            }

            return [
                'id' => $row->id,
                'date_of_issue' => $row->date_of_issue,
                'customer_name' => $row->customer_name,
                'customer_id' => $row->customer_id,
                'number_full' => $row->number_full,
                'total' => number_format((float) $row->total,2, ".", ""),
                'total_to_pay' => number_format($total_to_pay,2, ".", ""),
                'type' => $row->type,
                'guides' => $guides,
                'date_payment_last' => ($date_payment_last) ? $date_payment_last->date_of_payment->format('Y-m-d') : null,
                'delay_payment' => $delay_payment,
                'date_of_due' =>  $date_of_due,
                'currency_type_id' => $row->currency_type_id,
                'exchange_rate_sale' => (float)$row->exchange_rate_sale,
                "user_id"=> $row->user_id,
                "username" => $row->username,
                "total_subtraction" => $row->total_subtraction,
                "total_payment" => $row->total_payment,
            ];
    
        });

    }
 
}
