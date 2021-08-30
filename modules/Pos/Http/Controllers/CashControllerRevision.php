<?php

namespace Modules\Pos\Http\Controllers;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Cash;
use App\Models\Tenant\Company;
use App\Models\Tenant\PaymentMethodType;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Modules\Pos\Exports\ReportCashExport;
use Modules\Pos\Mail\CashEmail;
use Illuminate\Support\Facades\Mail;

class CashControllerRevision extends Controller
{
    public function email(Request $request)
    {
        $request->validate(
            ['email' => 'required']
        );

        $company = Company::active();
        $email = $request->input('email');

        Configuration::setConfigSmtpMail();
        Mail::to($email)->send(new CashEmail($company, $this->getPdf($request->cash_id)));

        return [
            'success' => true
        ];
    }

    private function getPdf($cash, $format = 'ticket')
    {
        $data = $this->generateData($cash);
//        dd($data);
//        $cash = Cash::findOrFail($cash);
//        $company = Company::first();
//
//        $methods_payment = collect(PaymentMethodType::all())->transform(function ($row) {
//            return (object)[
//                'id' => $row->id,
//                'name' => $row->description,
//                'sum' => 0
//            ];
//        });

        set_time_limit(0);

        $quantity_rows = 30;//$cash->cash_documents()->count();

        $html = view('pos::cash.report_pdf_' . $format, compact('data'))->render();
        $width = 78;
        if ($format === 'ticket') {
            $pdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => [
                    $width,
                    190 +
                    ($quantity_rows * 8)
                ],
                'margin_top' => 5,
                'margin_right' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5
            ]);
        } else {
            $pdf = new Mpdf([
                'mode' => 'utf-8'
            ]);
        }

        $pdf->WriteHTML($html);

        return $pdf->output('', 'S');
    }

    public function reportTicket($cash)
    {
        $temp = tempnam(sys_get_temp_dir(), 'cash_pdf_ticket');
        file_put_contents($temp, $this->getPdf($cash, 'ticket'));

        return response()->file($temp);
    }

    public function reportA4($cash)
    {
        $temp = tempnam(sys_get_temp_dir(), 'cash_pdf_a4');
        file_put_contents($temp, $this->getPdf($cash, 'a4'));

        return response()->file($temp);
    }

    public function reportExcel($cash)
    {
        $data = $this->generateData($cash);
//        $methods_payment = collect(PaymentMethodType::all())->transform(function ($row) {
//            return (object)[
//                'id' => $row->id,
//                'name' => $row->description,
//                'sum' => 0
//            ];
//        });

        set_time_limit(0);
        $filename = "Reporte_POS - {$cash->user->name} - {$cash->date_opening} {$cash->time_opening}";

        return (new ReportCashExport)
            ->getData($data)
            ->download($filename . '.xlsx');
    }

    private function generateData($cash_id)
    {
        $cash = Cash::query()->find($cash_id);
        $payment_methods = PaymentMethodType::all()->transform(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->description,
                'sum' => 0
            ];
        });

        $company = Company::query()->first();
        $data_company = [
            'name' => $company->name,
            'number' => $company->number,
        ];

        $establishment = $cash->user->establishment;
        $data_establishment = [
            'address' => "{$establishment->address} -
                          {$establishment->department->description} -
                          {$establishment->district->description}",
        ];

        $data_cash = [
            'seller' => $cash->user->name,
            'state' => $cash->state,
            'state_name' => $cash->state ? 'Aperturada' : 'Cerrada',
            'date_opening' => $cash->date_opening,
            'time_opening' => $cash->time_opening,
            'date_closed' => $cash->date_closed,
            'time_closed' => $cash->time_closed,
        ];

//        $cash_documents = $cash->cash_documents;
        $final_balance = 0;
        $cash_income = 0;
        $cash_egress = 0;
        $cash_final_balance = 0;

        $sale_notes = [];
        $documents = [];
        $technical_services = [];
        $expense_payments = [];
//        dd($cash->cash_documents);
        foreach ($cash->cash_documents as $cash_document) {
            if ($cash_document->sale_note) {
                if (in_array($cash_document->sale_note->state_type_id, ['01', '03', '05', '07', '13'])) {
                    if (count($cash_document->sale_note->payments) > 0) {
                        if ($cash_document->sale_note->currency_type_id == 'PEN') {
                            $cash_income += $cash_document->sale_note->total;
                            $final_balance += $cash_document->sale_note->total;
                        } else {
                            $cash_income += $cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale;
                            $final_balance += $cash_document->sale_note->total * $cash_document->sale_note->exchange_rate_sale;
                        }

//                        $pays = $cash_document->sale_note->payments;
//                        foreach ($payment_methods as $record) {
//                            $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->sum('payment'));
//                        }

                        $sale_notes[] = [
                            'number' => $cash_document->sale_note->number_full,
                            'date_of_issue' => $cash_document->sale_note->date_of_issue->format('Y-m-d'),
                            'customer_name' => $cash_document->sale_note->customer->name,
                            'customer_number' => $cash_document->sale_note->customer->number,
                            'currency_type_id' => $cash_document->sale_note->currency_type_id,
                            'total' => $cash_document->sale_note->total
                        ];
                    }
                }
            }
            if ($cash_document->document) {
                if (in_array($cash_document->document->state_type_id, ['01', '03', '05', '07', '13'])) {
                    if ($cash_document->document->currency_type_id == 'PEN') {
                        $cash_income += $cash_document->document->total;
                        $final_balance += $cash_document->document->total;
                    } else {
                        $cash_income += $cash_document->document->total * $cash_document->document->exchange_rate_sale;
                        $final_balance += $cash_document->document->total * $cash_document->document->exchange_rate_sale;
                    }
                }
                $payment_condition_id = $cash_document->document->payment_condition_id;
                if (in_array($cash_document->document->state_type_id, ['01', '03', '05', '07', '13']) && $payment_condition_id === '01') {
                    if (count($cash_document->document->payments) > 0) {
//                        $pays = $cash_document->document->payments;
//                        foreach ($payment_methods as $record) {
//                            $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->whereIn('document.state_type_id', ['01', '03', '05', '07', '13'])->sum('payment'));
//                        }
                    }
                } else {
//                    foreach ($payment_methods as $record) {
//                        if ($record->id === '09') {
//                            $record->sum += $cash_document->document->total;
//                        }
//                    }
                }
            }
            if ($cash_document->technical_service) {
                $cash_income += $cash_document->technical_service->cost;
                $final_balance += $cash_document->technical_service->cost;
                if (count($cash_document->technical_service->payments) > 0) {
//                    $pays = $cash_document->technical_service->payments;
//                    foreach ($payment_methods as $record) {
//                        $record->sum = ($record->sum + $pays->where('payment_method_type_id', $record->id)->sum('payment'));
//                    }

                    $technical_services[] = [
                        'number' => 'TS-'.$cash_document->technical_service->id,
                        'date_of_issue' => $cash_document->technical_service->date_of_issue->format('Y-m-d'),
                        'customer_name' => $cash_document->technical_service->customer->name,
                        'customer_number' => $cash_document->technical_service->customer->number,
                        'currency_type_id' => 'PEN',
                        'total' => $cash_document->technical_service->cost
                    ];
                }
            }
            if ($cash_document->expense_payment) {
                if ($cash_document->expense_payment->expense->state_type_id == '05') {
                    if ($cash_document->expense_payment->expense->currency_type_id == 'PEN') {
                        $cash_egress += $cash_document->expense_payment->payment;
                        $final_balance -= $cash_document->expense_payment->payment;
                    } else {
                        $cash_egress += $cash_document->expense_payment->payment * $cash_document->expense_payment->expense->exchange_rate_sale;
                        $final_balance -= $cash_document->expense_payment->payment * $cash_document->expense_payment->expense->exchange_rate_sale;
                    }

                    $expense_payments[] = [
                        'number' => $cash_document->expense_payment->expense->number,
                        'date_of_issue' => $cash_document->technical_service->date_of_issue->format('Y-m-d'),
                        'customer_name' => $cash_document->technical_service->customer->name,
                        'customer_number' => $cash_document->technical_service->customer->number,
                        'currency_type_id' => 'PEN',
                        'total' => $cash_document->technical_service->cost
                    ];
                }
            }
        }

        return [
            'company' => $data_company,
            'establishment' => $data_establishment,
            'cash' => $data_cash,
            'sale_notes' => $sale_notes,
            'technical_services' => $technical_services,
            'documents' => $documents,
            'expense_payments' => $expense_payments,
        ];
//        $cash_final_balance = $final_balance + $cash->beginning_balance;
    }
}
