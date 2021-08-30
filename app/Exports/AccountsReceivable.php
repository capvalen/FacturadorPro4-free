<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNotePayment;
use App\Models\Tenant\Invoice;
use Carbon\Carbon;


class AccountsReceivable implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
    /**/

     $document_payments = DB::table('document_payments')
            ->select('document_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('document_id');

           $documents = DB::connection('tenant')
                ->table('documents')
                ->join('persons', 'persons.id', '=', 'documents.customer_id')
                ->leftJoinSub($document_payments, 'payments', function ($join) {
                    $join->on('documents.id', '=', 'payments.document_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->whereIn('document_type_id', ['01','03','08'])
                ->select(DB::raw("documents.id as id, ".
                                    "DATE_FORMAT(documents.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                    "persons.name as customer_name, persons.id as customer_id, documents.document_type_id,".
                                    "CONCAT(documents.series,'-',documents.number) AS number_full, ".
                                    "documents.total as total, ".
                                    "IFNULL(payments.total_payment, 0) as total_payment, ".
                                    "'document' AS 'type', ". "documents.currency_type_id, " . "documents.exchange_rate_sale", "companies.trade_name"))
                ->where('total_canceled', 0);
                  $sale_note_payments = DB::table('sale_note_payments')
            ->select('sale_note_id', DB::raw('SUM(payment) as total_payment'))
            ->groupBy('sale_note_id');
   $company = DB::connection('tenant')->table('companies')
                            ->select('name', 'number')->get();

            $sale_notes = DB::connection('tenant')
                ->table('sale_notes')
                ->join('persons', 'persons.id', '=', 'sale_notes.customer_id')
                ->leftJoinSub($sale_note_payments, 'payments', function ($join) {
                    $join->on('sale_notes.id', '=', 'payments.sale_note_id');
                })
                ->whereIn('state_type_id', ['01','03','05','07','13'])
                ->select(DB::raw("sale_notes.id as id, ".
                                "DATE_FORMAT(sale_notes.date_of_issue, '%Y/%m/%d') as date_of_issue, ".
                                "persons.name as customer_name, persons.id as customer_id, null as document_type_id,".
                                "sale_notes.filename as number_full, ".
                                "sale_notes.total as total, ".
                                "IFNULL(payments.total_payment, 0) as total_payment, ".
                                "'sale_note' AS 'type', " . "sale_notes.currency_type_id, " . "sale_notes.exchange_rate_sale"))
                ->where('sale_notes.changed', false)
                ->where('sale_notes.total_canceled', false);

                $records = $documents->union($sale_notes)->get();

                $collection =  collect($records)->transform(function($row) {
                $total_to_pay = (float)$row->total - (float)$row->total_payment;
                $delay_payment = null;
                $date_of_due = null;
             
                if($total_to_pay > 0) {
                    if($row->document_type_id){

                        $invoice = Invoice::where('document_id', $row->id)->first();
                        if($invoice)
                        {
                            $due =   Carbon::parse($invoice->date_of_due); // $invoice->date_of_due;
                            $date_of_due = $invoice->date_of_due->format('Y-m-d');
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
                    'exchange_rate_sale' => (float)$row->exchange_rate_sale
                ];
//            }
        });
        
        return view('tenant.reports.no_paid.reportall_excel',['records' => $collection->all(),
            'companies' => $company ]);
        
    	/*$clients = DB::connection('tenant')
                            ->table('documents')
                            ->join('persons', 'documents.customer_id', '=', 'persons.id')
                            ->join('companies', 'documents.user_id', '=', 'companies.id')
                            ->select('companies.trade_name','companies.number','date_of_issue', 'time_of_issue','filename','persons.name', 'total_value','total', DB::raw('CONCAT(documents.series, "-", documents.number) AS full_number'))
                            ->where('total_canceled', 0)->get();
        return view('tenant.reports.no_paid.reportall_excel', ['records' => $clients]);*/
    }

}
