<?php
namespace Modules\Account\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tenant\Item;
use Illuminate\Http\Request;
use App\Models\Tenant\Document;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Modules\Account\Models\CompanyAccount;
use Modules\Account\Exports\ReportAccountingAdsoftExport;
use Modules\Account\Exports\ReportAccountingConcarExport;
use Modules\Account\Exports\ReportAccountingFoxcontExport;
use Modules\Account\Exports\ReportAccountingContasisExport;
use Modules\Account\Exports\ReportAccountingSumeriusExport;

class AccountController extends Controller
{
    public function index()
    {
        return view('account::accounting.index');
    }

    public function download(Request $request)
    {
        $type = $request->input('type');
        $month = $request->input('month');

        $d_start = Carbon::parse($month.'-01')->format('Y-m-d');
        $d_end = Carbon::parse($month.'-01')->endOfMonth()->format('Y-m-d');

        $records = $this->getDocuments($d_start, $d_end);
        $filename = 'Reporte_'.ucfirst($type).'_Ventas_'.date('YmdHis');

        switch ($type) {
            case 'concar':
                $data = [
                    'records' => $this->getStructureConcar($this->getAllDocuments($d_start, $d_end)),
                ];

                $report = (new ReportAccountingConcarExport)
                            ->data($data)
                            ->download($filename.'.xlsx');

                return $report;

            case 'siscont':

                $records = $this->getStructureSiscont($records);

                $temp = tempnam(sys_get_temp_dir(), 'txt');
                $file = fopen($temp, 'w+');
                foreach ($records as $record)
                {
                    $line = implode('', $record);
                    fwrite($file, $line."\r\n");
                }
                fclose($file);

                return response()->download($temp, $filename.'.txt');

            case 'foxcont':

                $data = [
                    'records' => $this->getStructureFoxcont($records),
                ];

                return (new ReportAccountingFoxcontExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

            case 'contasis':

                $data = [
                    'records' => $this->getStructureContasis($records),
                ];

                return (new ReportAccountingContasisExport)
                    ->data($data)
                    ->download($filename.'.xlsx');

            case 'adsoft':

                $data = [
                    'records' => $this->getStructureAdsoft($records),
                ];

                return (new ReportAccountingAdsoftExport)
                    ->data($data)
                    ->download($filename.'.xlsx');
            case 'sumerius':

                $data = [
                    'records' => $this->getStructureSumerius($records),
                ];

                return (new ReportAccountingSumeriusExport)
                    ->data($data)
                    ->download($filename.'.xlsx');
        }
    }

    private function getStructureSumerius($documents)
    {
        return $documents->transform(function($row) {
            return [
                'col_A' => number_format($row->id, 2, ".", ""),
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
		        'state_type_id' => $row->state_type_id,
                'series' => $row->series,
                'number' => str_pad($row->number, 7, '0', STR_PAD_LEFT),
                'col_G' => '',
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_plastic_bag_taxes' => number_format($row->total_plastic_bag_taxes, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_retention' => number_format(0, 2, ".", ""),
                'col_S' => '',
                'col_T' => '',
                'col_U' => '',
                'col_V' => '70121',
                'col_W' => '',
                'col_X' => '',
                'col_Y' => '401112',
                'col_Z' => '1212',
                'col_AA' => 'VENTA NACIONAL',
            ];
        });
    }

    private function getStructureAdsoft($documents)
    {
        $rows = [];
        foreach ($documents as $row)
        {
            $document = [
                'serie' => $row->series,
                'numero' => $row->number,
                'fecfac' => Carbon::parse($row->date_of_issue)->format('d/m/Y'),
                'fecven' => Carbon::parse($row->invoice->date_of_due)->format('d/m/Y'),
                'nro_ruc' => $row->customer->identity_document_type_id === '6' ? $row->customer->number : '',
                'nombre' => $row->customer->name,
                'tipdoc' => $row->document_type_id,
                'tipmon' => strtoupper($row->currency_type->description),
                'detrac' => '',
                'isc' => $row->state_type_id == '11' ? 0 : number_format($row->total_isc, 2, '.', ''),
                'icbper' => '',
                'imp_ina' => 0,
                'imp_exp' => '',
                'recargo' => '',
                'st' => $row->state_type_id === '11' ? 'A' : '',
                'ser_dqm' => '',
                'nro_dqm' => '',
                'fec_dqm' => '',
                'tip_dqm' => '',
                'serie_fin' => '',
                'numero_fin' => '',
                'nro_dni' => $row->customer->identity_document_type_id === '1' ? $row->customer->number : '',
                'pasaporte' => '',
                'cta_vta' => '',
                'tip_cam' => '',
            ];
			if ($row->state_type_id === '11') {
                $document['imp_exo'] = 0;
                $document['imp_vta'] = 0;
                $document['imp_tot'] = 0;
                $document['imp_igv'] = 0;
            } else {
                if ($row->total_exonerated == 0) {
                    $document['imp_exo'] = 0;
                    $document['imp_vta'] = number_format($row->total_value, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total, 2, '.', '');
                    $document['imp_igv'] = number_format($row->total_igv, 2, '.', '');
                } else {
                    $document['imp_exo'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_vta'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_tot'] = number_format($row->total_exonerated, 2, '.', '');
                    $document['imp_igv'] = 0;
                }
            }
            array_push($rows, $document);
        }
        return $rows;
    }

    private function getDocuments($d_start, $d_end)
    {
        return Document::query()
                                ->whereBetween('date_of_issue', [$d_start, $d_end])
                                ->whereIn('document_type_id', ['01', '03'])
                                ->whereIn('currency_type_id', ['PEN','USD'])
                                ->orderBy('series')
                                ->orderBy('number')
                                ->get();

    }

    private function getAllDocuments($d_start, $d_end)
    {
        return Document::query()
                                ->whereBetween('date_of_issue', [$d_start, $d_end])
                                ->whereIn('currency_type_id', ['PEN','USD'])
                                ->orderBy('series')
                                ->orderBy('number')
                                ->get();

    }

    private function getStructureFoxcont($documents)
    {

        return $documents->transform(function($row) {
            return [
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
                'state_type_id' => $row->state_type_id,
                'series' => $row->series,
                'number' => str_pad($row->number, 7, '0', STR_PAD_LEFT),
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_plastic_bag_taxes' => number_format($row->total_plastic_bag_taxes, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_retention' => number_format(0, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
            ];
        });

    }

    private function getShortDocumentType($document_type_id)
    {

        $document_type = "";

        switch ($document_type_id) {
            case '01':
                $document_type = 'FT';
                break;
            case '03':
                $document_type = 'BV';
                break;
            case '07':
                $document_type = 'NA';
                break;
            case '08':
                $document_type = 'ND';
                break;
        }

        return $document_type;

    }


    private function getStructureConcar($documents)
    {
        $company_account = CompanyAccount::first();
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $currency_type_id = ($row->currency_type_id === 'PEN')?'MN':'US';
            $document_type_id = $this->getShortDocumentType($row->document_type_id);
            $detail = $row->customer->name.', '.$document_type_id.' '.$row->number_full;
            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);

            $main_gloss = 'VENTAS DEL DIA '.$date_of_issue->format('dmY');
            $date_of_due = ($row->invoice) ? $row->invoice->date_of_due->format('d/m/Y') : '';

            $reference_document_type_id = '';
            $reference_number_full = '';
            $reference_date_of_issue = '';
            $reference_total_value = '';
            $reference_total_igv = '';

            if(in_array($row->document_type_id, ['07', '08']))
            {
                $reference_document_type_id = $this->getShortDocumentType($row->note->affected_document->document_type_id);
                $reference_number_full = $row->note->affected_document->number_full;
                $reference_date_of_issue = $row->note->affected_document->date_of_issue->format('d/m/Y');
                $reference_total_value = $row->note->affected_document->total_value;
                $reference_total_igv = $row->note->affected_document->total_igv;

            }

            foreach ($row->items as $item) {

                if(in_array($row->document_type_id, ['07']))
                {

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '121201',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total / $row->exchange_rate_sale, 2, ".", "") : $item->total),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total : number_format($item->total * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        // 'col_W' => $detail,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '401111',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->igv_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_igv,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_igv / $row->exchange_rate_sale, 2, ".", "") : $item->total_igv),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_igv : number_format($item->total_igv * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '704101',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_value,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_value / $row->exchange_rate_sale, 2, ".", "") : $item->total_value),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_value : number_format($item->total_value * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                }
                else
                {

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '121201',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'D',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total / $row->exchange_rate_sale, 2, ".", "") : $item->total),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total : number_format($item->total * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        // 'col_W' => $detail,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '401111',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->igv_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_igv,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_igv / $row->exchange_rate_sale, 2, ".", "") : $item->total_igv),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_igv : number_format($item->total_igv * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];

                    $rows[] = [
                        // 'col_A' => '',
                        'col_B' => '05',
                        'col_C' => $number_index,
                        'col_D' => $date_of_issue->format('d/m/Y'),
                        'col_E' => $currency_type_id,
                        'col_F' => $main_gloss,
                        // 'col_F' => 'POR VENTA',
                        'col_G' => $row->exchange_rate_sale,
                        'col_H' => 'V',
                        'col_I' => 'S',
                        'col_J' => '',
                        // 'col_K' => '704101',
                        'col_K' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                        'col_L' => $row->customer->number,
                        'col_M' => '',
                        'col_N' => 'H',
                        'col_O' => ($row->state_type_id == 11) ? 0 : $item->total_value,
                        'col_P' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? number_format($item->total_value / $row->exchange_rate_sale, 2, ".", "") : $item->total_value),
                        'col_Q' => ($row->state_type_id == 11) ? 0 : ( ($row->currency_type_id === 'PEN') ? $item->total_value : number_format($item->total_value * $row->exchange_rate_sale, 2, ".", "")),
                        'col_R' => $document_type_id,
                        'col_S' => $row->number_full,
                        'col_T' => $row->date_of_issue->format('d/m/Y'),
                        'col_U' => $date_of_due,
                        'col_V' => '',
                        'col_W' => $document_type_id.'-'.$row->number_full,
                        'col_X' => '',
                        'col_Y' => '',
                        'col_Z' => $reference_document_type_id,
                        'col_AA' => $reference_number_full,
                        'col_AB' => $reference_date_of_issue,
                        'col_AC' => '',
                        'col_AD' => $reference_total_value,
                        'col_AE' => $reference_total_igv,
                        'col_AF' => '',
                        'col_AG' => '',
                        'col_AH' => '',
                        'col_AI' => '',
                        'col_AJ' => '',
                        'col_AK' => '',
                        'col_AL' => '',
                        'col_AM' => '',
                        'col_AN' => '',
                    ];
                }

            }

        }
        return $rows;
    }

    private function getStructureSiscont($documents)
    {

        $company_account = CompanyAccount::first();
        $rows = [];
        foreach ($documents as $index => $row)
        {
            $date_of_issue = Carbon::parse($row->date_of_issue);
            $currency_type_id = ($row->currency_type_id === 'PEN')?'S':'D';
            $document_type_id = ($row->document_type_id === '01')?'01':'03';
            $detail = substr($row->customer->name.', '.$document_type_id.' '.$row->number_full, 0, 60);

            $number_index = $date_of_issue->format('m').str_pad($index + 1, 4, "0", STR_PAD_LEFT);

            foreach ($row->items as $item) {


                $rows[] = [
                    'col_001_002' => '02',
                    'col_003_006' => $number_index,
                    'col_007_014' => $date_of_issue->format('d/m/y'),
                    // 'col_015_024' => '12102',
                    'col_015_024' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                    'col_025_036' => ($row->state_type_id == '11') ? str_pad(0, 12, '0', STR_PAD_LEFT) : str_pad($item->total, 12, '0', STR_PAD_LEFT),
                    'col_037_037' => 'D',
                    'col_038_038' => $currency_type_id,
                    'col_039_048' => str_pad(number_format($row->exchange_rate_sale, 7), 10, '0', STR_PAD_LEFT),
                    'col_049_050' => $document_type_id,
                    'col_051_070' => $row->series.'-'.str_pad($row->number, 15,'0', STR_PAD_LEFT),
                    'col_071_078' => str_pad(($row->date_of_due)?$row->date_of_due->format('d/m/y'):$row->date_of_issue->format('d/m/y'), 8,' ', STR_PAD_LEFT),
                    'col_079_089' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_090_099' => str_pad('', 10, ' ', STR_PAD_LEFT),
                    'col_100_103' => str_pad('', 4, ' ', STR_PAD_LEFT),
                    'col_104_113' => str_pad('', 10, ' ', STR_PAD_LEFT),
                    'col_114_114' => str_pad('', 1, ' ', STR_PAD_LEFT),
                    'col_115_122' => $date_of_issue->format('d/m/y'),
                    'col_123_134' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_135_146' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_147_158' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_159_170' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_171_182' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_183_193' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_194_194' => str_pad('', 1, ' ', STR_PAD_LEFT),
                    'col_195_234' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_235_264' => str_pad($detail, 30, ' ', STR_PAD_LEFT),
                    'col_265_265' => $row->customer->identity_document_type_id,
                    'col_266_268' => str_pad('', 3, ' ', STR_PAD_LEFT),
                    'col_269_288' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_289_308' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_309_328' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_329_348' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_349_350' => str_pad('', 2, ' ', STR_PAD_LEFT),
                    'col_351_358' => str_pad('', 8, ' ', STR_PAD_LEFT),
                ];

                $rows[] = [
                    'col_001_002' => '02',
                    'col_003_006' => $number_index,
                    'col_007_014' => $date_of_issue->format('d/m/y'),
                    // 'col_015_024' => '40111',
                    'col_015_024' =>  ($row->currency_type_id === 'PEN') ? $company_account->igv_pen : $company_account->igv_usd,
                    // 'col_025_036' => str_pad($row->total, 12, '0', STR_PAD_LEFT),
                    'col_025_036' => ($row->state_type_id == '11') ? str_pad(0, 12, '0', STR_PAD_LEFT) : str_pad($item->total_igv, 12, '0', STR_PAD_LEFT),
                    'col_037_037' => 'H',
                    'col_038_038' => $currency_type_id,
                    'col_039_048' => str_pad(number_format($row->exchange_rate_sale, 7), 10, '0', STR_PAD_LEFT),
                    'col_049_050' => $document_type_id,
                    'col_051_070' => $row->series.'-'.str_pad($row->number, 15,'0', STR_PAD_LEFT),
                    'col_071_078' => str_pad(($row->date_of_due)?$row->date_of_due->format('d/m/y'):$row->date_of_issue->format('d/m/y'), 8,' ', STR_PAD_LEFT),
                    'col_079_089' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_090_099' => str_pad('', 10, ' ', STR_PAD_LEFT),
                    'col_100_103' => str_pad('', 4, ' ', STR_PAD_LEFT),
                    'col_104_113' => str_pad('', 10, ' ', STR_PAD_LEFT),
                    'col_114_114' => 'V',
                    'col_115_122' => $date_of_issue->format('d/m/y'),
                    'col_123_134' => ($row->state_type_id == '11') ? str_pad(0, 12, '0', STR_PAD_LEFT) : str_pad($item->total_value, 12, '0', STR_PAD_LEFT),
                    'col_135_146' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_147_158' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    'col_159_170' => str_pad('', 12, ' ', STR_PAD_LEFT),
                    // 'col_171_182' => str_pad($item->total_igv, 12, '0', STR_PAD_LEFT),
                    'col_171_182' => ($row->state_type_id == '11') ? str_pad(0, 12, '0', STR_PAD_LEFT) : str_pad($item->total_igv, 12, '0', STR_PAD_LEFT),
                    'col_183_193' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_194_194' => str_pad('', 1, ' ', STR_PAD_LEFT),
                    'col_195_234' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                    'col_235_264' => str_pad($detail, 30, ' ', STR_PAD_LEFT),
                    'col_265_265' => $row->customer->identity_document_type_id,
                    'col_266_268' => str_pad('', 3, ' ', STR_PAD_LEFT),
                    'col_269_288' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_289_308' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_309_328' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_329_348' => str_pad('', 20, ' ', STR_PAD_LEFT),
                    'col_349_350' => str_pad('', 2, ' ', STR_PAD_LEFT),
                    'col_351_358' => str_pad('', 8, ' ', STR_PAD_LEFT),
                ];

                if($row->state_type_id != '11'){

                    $rows[] = [
                        'col_001_002' => '02',
                        'col_003_006' => $number_index,
                        'col_007_014' => $date_of_issue->format('d/m/y'),
                        // 'col_015_024' => '70201',
                        'col_015_024' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                        'col_025_036' => str_pad($item->total_value, 12, '0', STR_PAD_LEFT),
                        'col_037_037' => 'H',
                        'col_038_038' => $currency_type_id,
                        'col_039_048' => str_pad(number_format($row->exchange_rate_sale, 7), 10, '0', STR_PAD_LEFT),
                        'col_049_050' => $document_type_id,
                        'col_051_070' => $row->series.'-'.str_pad($row->number, 15,'0', STR_PAD_LEFT),
                        'col_071_078' => str_pad(($row->date_of_due)?$row->date_of_due->format('d/m/y'):$row->date_of_issue->format('d/m/y'), 8,' ', STR_PAD_LEFT),
                        'col_079_089' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                        'col_090_099' => str_pad('', 10, ' ', STR_PAD_LEFT),
                        'col_100_103' => str_pad('', 4, ' ', STR_PAD_LEFT),
                        'col_104_113' => str_pad('', 10, ' ', STR_PAD_LEFT),
                        'col_114_114' => str_pad('', 1, ' ', STR_PAD_LEFT),
                        'col_115_122' => $date_of_issue->format('d/m/y'),
                        'col_123_134' => str_pad('', 12, ' ', STR_PAD_LEFT),
                        'col_135_146' => str_pad('', 12, ' ', STR_PAD_LEFT),
                        'col_147_158' => str_pad('', 12, ' ', STR_PAD_LEFT),
                        'col_159_170' => str_pad('', 12, ' ', STR_PAD_LEFT),
                        'col_171_182' => str_pad('', 12, ' ', STR_PAD_LEFT),
                        'col_183_193' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                        'col_194_194' => str_pad('', 1, ' ', STR_PAD_LEFT),
                        'col_195_234' => str_pad($row->customer->number, 11, ' ', STR_PAD_LEFT),
                        'col_235_264' => str_pad($detail, 30, ' ', STR_PAD_LEFT),
                        'col_265_265' => $row->customer->identity_document_type_id,
                        'col_266_268' => str_pad('', 3, ' ', STR_PAD_LEFT),
                        'col_269_288' => str_pad('', 20, ' ', STR_PAD_LEFT),
                        'col_289_308' => str_pad('', 20, ' ', STR_PAD_LEFT),
                        'col_309_328' => str_pad('', 20, ' ', STR_PAD_LEFT),
                        'col_329_348' => str_pad('', 20, ' ', STR_PAD_LEFT),
                        'col_349_350' => str_pad('', 2, ' ', STR_PAD_LEFT),
                        'col_351_358' => str_pad('', 8, ' ', STR_PAD_LEFT),
                    ];

                }

            }


        }
        return $rows;
    }

    private function getStructureContasis($documents)
    {

        return $documents->transform(function($row) {
            $company_account = CompanyAccount::first();
            $document_base = ($row->note) ? $row->note : null;
            $payment_condition = '';
            $payment_method = '';

            if($row->payments->count() > 0){
                if($row->payments[0]->payment_method_type_id == '01') {
                    $payment_condition = 'CON';
                    $payment_method = '008';
                }elseif($row->payments[0]->payment_method_type_id == '09'){
                    $payment_condition = 'CRE';
                    $payment_method = '005';
                }
            }else{
                $payment_condition = '';
                $payment_method = '';
            }
            return [
                'date_of_issue' => $row->date_of_issue->format('d/m/Y'),
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'document_type_id' => $row->document_type_id,
                'state_type_id' => $row->state_type_id,
                'series' => '00'.$row->series,
                'number' => str_pad($row->number, 13, '0', STR_PAD_LEFT),
                'customer_identity_document_type_id' => $row->customer->identity_document_type_id,
                'customer_number' => $row->customer->number,
                'customer_name' => $row->customer->name,

                'total_exportation' => number_format($row->total_exportation, 2, ".", ""),
                'total_taxed' => number_format($row->total_taxed, 2, ".", ""),
                'total_exonerated' => number_format($row->total_exonerated, 2, ".", ""),
                'total_unaffected' => number_format($row->total_unaffected, 2, ".", ""),
                'total_isc' => number_format($row->total_isc, 2, ".", ""),
                'total_igv' => number_format($row->total_igv, 2, ".", ""),
                'total_other_taxes' => number_format($row->total_total_other_taxes, 2, ".", ""),
                'total' => number_format($row->total, 2, ".", ""),
                'exchange_rate_sale' => number_format($row->exchange_rate_sale, 2, ".", ""),
                'db_date_issue' => ($document_base) ? $document_base->affected_document->date_of_issue->format('d/m/Y') : '',
                'db_document_type_id' => ($document_base) ? $document_base->affected_document->document_type_id : '',
                'db_series' => ($document_base) ? $document_base->affected_document->series : '',
                'db_number' => ($document_base) ? str_pad($document_base->affected_document->number, 13, '0', STR_PAD_LEFT) : '',
                'currency' => ($row->currency_type_id === 'PEN')?'S':'D',
                'amount_usd' => null,
                'date_of_due' => $row->invoice->date_of_due->format('d/m/Y'),
                'payment_condition' => $payment_condition,
                'account_taxed' => ($row->currency_type_id === 'PEN') ? $company_account->subtotal_pen : $company_account->subtotal_usd,
                'account_total' => ($row->currency_type_id === 'PEN') ? $company_account->total_pen : $company_account->total_usd,
                'aditional_information' => $row->aditional_information,
                'payment_method' => $payment_method,
            ];
        });
    }
}
