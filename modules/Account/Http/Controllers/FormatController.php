<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use App\Models\Tenant\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Account\Exports\ReportFormatPurchaseExport;
use Modules\Account\Exports\ReportFormatSaleExport;

class FormatController extends Controller
{
    public function index()
    {
        return view('account::account.format');
    }

    public function download(Request $request)
    {
        $type = $request->input('type');
        $month = $request->input('month');
        $d_start = Carbon::parse($month.'-01')->format('Y-m-d');
        $d_end = Carbon::parse($month.'-01')->endOfMonth()->format('Y-m-d');

        if($type === 'sale') {
            $filename = 'Reporte_Formato_Ventas_'.date('YmdHis');
            $data = [
                'period' => $month,
                'company' => $this->getCompany(),
                'records' => $this->getSaleDocuments($d_start, $d_end)
            ];

            return (new ReportFormatSaleExport())
                ->data($data)
                ->download($filename.'.xlsx');
        } else {
            $filename = 'Reporte_Formato_Compras_'.date('YmdHis');
            $data = [
                'period' => $month,
                'company' => $this->getCompany(),
                'records' => $this->getPurchaseDocuments($d_start, $d_end)
            ];

            return (new ReportFormatPurchaseExport())
                ->data($data)
                ->download($filename.'.xlsx');
        }
    }

    private function getCompany()
    {
        $company = Company::query()->first();

        return [
            'name' => $company->name,
            'number' => $company->number,
        ];
    }

    /**
     * Establece a 0 los totales para los documentos que se habiliten en $type_document_to_evalue
     * y que el status se encuentre en $type_document_to_evalue.
     *
     * Normalmente se evalua Factura electronica (01) y Boleta de venta electronica (03)
     * Si $is_affected es verdadero, evalua tambien nota de credito (07) y debito (08)
     *
     * @param Document $row
     * @param boolean  $is_affected
     *
     * @return Document
     */
    public function AdjustValueToReportByDocumentTypeAndStateType(Document $row, $is_affected = false){

        $document_type_id = $row->document_type_id;
        $state_type_id = $row->state_type_id;
        $type_document_to_evalue = [
            '01',//    FACTURA ELECTRÓNICA
            '03',//    BOLETA DE VENTA ELECTRÓNICA
            //'07',//    NOTA DE CRÉDITO
            //'08',//    NOTA DE DÉBITO
            //'09',//    GUIA DE REMISIÓN REMITENTE
            //'20',//    COMPROBANTE DE RETENCIÓN ELECTRÓNICA
            //'31',//    Guía de remisión transportista
            //'40',//    COMPROBANTE DE PERCEPCIÓN ELECTRÓNICA
            //'71',//    Guia de remisión remitente complementaria
            //'72',//	Guia de remisión transportista complementaria
            //'GU75',//	GUÍA
            //'NE76',//	NOTA DE ENTRADA
            //'80',//	NOTA DE VENTA
            //'02',//	RECIBO POR HONORARIOS
            //'14',//	SERVICIOS PÚBLICOS
        ];
        if($is_affected == true){
            $type_document_to_evalue = [
                '01',//    FACTURA ELECTRÓNICA
                '03',//    BOLETA DE VENTA ELECTRÓNICA
                '07',//    NOTA DE CRÉDITO
                '08',//    NOTA DE DÉBITO
            ];
        }
        $document_state_to_evalue = [
            // '01',//	Registrado
            // '03',//	Enviado
            // '05',//	Aceptado
            // '07',//	Observado
            '09',//	Rechazado
            '11',//	Anulado
            // '13',//	Por anular
        ];
        if (
            in_array($document_type_id, $type_document_to_evalue) &&
            in_array($state_type_id, $document_state_to_evalue)
        ) {
            $row->total_exportation = 0 ;
            $row->total_taxed = 0 ;
            $row->total_exonerated = 0 ;
            $row->total_unaffected = 0 ;
            $row->total_plastic_bag_taxes = 0 ;
            $row->total_igv = 0 ;
            $row->total = 0 ;
        }
        return $row;
    }

    private function getSaleDocuments($d_start, $d_end)
    {
        return Document::query()
            ->whereBetween('date_of_issue', [$d_start, $d_end])
            // ->whereIn('document_type_id', ['01', '03'])
            ->whereIn('currency_type_id', ['PEN', 'USD'])
            ->orderBy('series')
            ->orderBy('number')
            ->get()->transform(function ($row) {
                $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row);
                $note_affected_document = new Document();
                if (!empty($row->note)) {
                    if (!empty($row->note->affected_document)) {
                        $note_affected_document = $row->note->affected_document;
                        $row = $this->AdjustValueToReportByDocumentTypeAndStateType($row,1);
                    }
                }

                $total = $row->total;
                $total_taxed = $row->total_taxed;
                $symbol = $row->currency_type->symbol;
                $total_igv = $row->total_igv;

                if ($row->currency_type_id == 'USD') {
                    $total = round($row->total * $row->exchange_rate_sale, 2);
                    $total_taxed = round($row->total_taxed * $row->exchange_rate_sale, 2);
                    $symbol = 'S/';
                    $total_igv = round($row->total_igv * $row->exchange_rate_sale, 2);
                }

                return [
                    'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                    'document_type_id' => $row->document_type_id,
                    'state_type_id' => $row->state_type_id,
                    'series' => $row->series,
                    'number' => $row->number,
                    'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                    'customer_number' => $row->customer->number,
                    'customer_name' => $row->customer->name,
                    'total_exportation' => $row->total_exportation,
                    'total_taxed' => $total_taxed,
                    'total_exonerated' => $row->total_exonerated,
                    'total_unaffected' => $row->total_unaffected,
                    'total_plastic_bag_taxes' => $row->total_plastic_bag_taxes,
                    'total_isc' => $row->total_isc,
                    'total_igv' => $total_igv,
                    'total' => $total,
                    'exchange_rate_sale' => $row->exchange_rate_sale,
                    'currency_type_symbol' => $symbol,
                    'affected_document' => (in_array($row->document_type_id, ['07', '08'])) ? [
                        'date_of_issue' => !empty($note_affected_document->date_of_issue)?$note_affected_document->date_of_issue->format('d/m/Y'):null,
                        'document_type_id' => $note_affected_document->document_type_id,
                        'series' => $note_affected_document->series,
                        'number' => $note_affected_document->number,

                    ] : null
                ];
            });

    }

    private function getPurchaseDocuments($d_start, $d_end)
    {
        return Purchase::query()
            ->whereBetween('date_of_issue', [$d_start, $d_end])
            ->whereIn('document_type_id', ['01', '03', '14'])
            ->whereIn('currency_type_id', ['PEN'])
            ->orderBy('series')
            ->orderBy('number')
            ->get()->transform(function($row) {
                return [
                    'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                    'date_of_due' => $row->date_of_due->format('d/m/Y'),
                    'state_type_id' => $row->state_type_id,
                    'document_type_id' => $row->document_type_id,
                    'series' => $row->series,
                    'number' => $row->number,
                    'supplier_identity_document_type_id' => $row->supplier->identity_document_type_id,
                    'supplier_number' => $row->supplier->number,
                    'supplier_name' => $row->supplier->name,
                    'total_exportation' => $row->total_exportation,
                    'total_taxed' => $row->total_taxed,
                    'total_exonerated' => $row->total_exonerated,
                    'total_unaffected' => $row->total_unaffected,
                    'total_isc' => $row->total_isc,
                    'total_igv' => $row->total_igv,
                    'total' => $row->total,
                    'exchange_rate_sale' => $row->exchange_rate_sale,
                    'currency_type_symbol' => $row->currency_type->symbol
                ];
            });

    }
}
