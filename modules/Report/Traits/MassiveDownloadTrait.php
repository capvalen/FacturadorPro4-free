<?php

namespace Modules\Report\Traits;

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use App\CoreFacturalo\Template;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Company;
use App\Models\Tenant\{
    Document,
    SaleNote,
    Dispatch
};


trait MassiveDownloadTrait
{

    public function getData($document_types, $params){

        $documents_01 = [];
        $documents_03 = [];
        $sale_notes = [];
        $dispatches = [];

        foreach ($document_types as $document_type) {

            switch ($document_type) {
                case '01':
                    $documents_01 = $this->getRecordsByModel(Document::class, $params)->where('document_type_id', '01')->get();
                    break;
                case '03':
                    $documents_03 = $this->getRecordsByModel(Document::class, $params)->where('document_type_id', '03')->get();
                    break;
                case '80':
                    $sale_notes = $this->getRecordsByModel(SaleNote::class, $params)->get();
                    break;
                case '09':
                    $dispatches = $this->getRecordsByModel(Dispatch::class, $params)->get();
                    break;
                default:
                    $documents_01 = $this->getRecordsByModel(Document::class, $params)->whereIn('document_type_id', ['01', '03'])->get();
                    $sale_notes = $this->getRecordsByModel(SaleNote::class, $params)->get();
                    $dispatches = $this->getRecordsByModel(Dispatch::class, $params)->get();
                    break;
            }

        }

        return [
            'documents_01' => $documents_01,
            'documents_03' => $documents_03,
            'sale_notes' => $sale_notes,
            'dispatches' => $dispatches,
        ];

    }


    public function getTotals($document_types, $params){

        $total_documents = 0;

        foreach ($document_types as $document_type) {

            switch ($document_type) {
                case '01':
                    $total_documents += $this->getRecordsByModel(Document::class, $params)->where('document_type_id', '01')->count();
                    break;
                case '03':
                    $total_documents += $this->getRecordsByModel(Document::class, $params)->where('document_type_id', '03')->count();
                    break;
                case '80':
                    $total_documents += $this->getRecordsByModel(SaleNote::class, $params)->count();
                    break;
                case '09':
                    $total_documents += $this->getRecordsByModel(Dispatch::class, $params)->count();
                    break;
                default:
                    $total_documents += $this->getRecordsByModel(Document::class, $params)->whereIn('document_type_id', ['01', '03'])->count();
                    $total_documents += $this->getRecordsByModel(SaleNote::class, $params)->count();
                    $total_documents += $this->getRecordsByModel(Dispatch::class, $params)->count();
                    break;
            }

        }

        return $total_documents;

    }


    public function getRecordsByModel($model, $params){

        $records = $model::whereBetween('date_of_issue', [$params->date_start, $params->date_end])
                            ->latest()
                            ->whereTypeUser();

        if($params->person_id){
            $records = $records->where('customer_id', $params->person_id);
        }
        if($model == Document::class || $model == SaleNote::class){
            if($params->series){
                $records = $records->where('series', $params->series);
            }
            if($params->sellers && $model == Document::class){
                $records = $records->wherein('seller_id', $params->sellers);
            }
        }

        return $records;
    }


    public function toPrintByView($folder, $view) {

        $temp = tempnam(sys_get_temp_dir(), $folder);
        file_put_contents($temp, $view);

        return response()->file($temp);

    }


    public function createPdf($data) {

        ini_set("pcre.backtrack_limit", "5000000");

        $template = new Template();
        $format_pdf = "a4";
        $configuration = Configuration::first();
        $base_pdf_template = $configuration->formats;
        $company = Company::active();

        $pdf_margin_top = 15;
        $pdf_margin_right = 15;
        $pdf_margin_bottom = 15;
        $pdf_margin_left = 15;

        if ($base_pdf_template === 'full_height') {
            $pdf_margin_top = 5;
            $pdf_margin_right = 5;
            $pdf_margin_bottom = 5;
            $pdf_margin_left = 5;
        }

        $pdf_font_regular = config('tenant.pdf_name_regular');
        $pdf_font_bold = config('tenant.pdf_name_bold');

        if ($pdf_font_regular != false) {

            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $pdf = new Mpdf([
                'fontDir' => array_merge($fontDirs, [
                    app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
                                                DIRECTORY_SEPARATOR.'pdf'.
                                                DIRECTORY_SEPARATOR.$base_pdf_template.
                                                DIRECTORY_SEPARATOR.'font')
                ]),
                'fontdata' => $fontData + [
                    'custom_bold' => [
                        'R' => $pdf_font_bold.'.ttf',
                    ],
                    'custom_regular' => [
                        'R' => $pdf_font_regular.'.ttf',
                    ],
                ],
                'margin_top' => $pdf_margin_top,
                'margin_right' => $pdf_margin_right,
                'margin_bottom' => $pdf_margin_bottom,
                'margin_left' => $pdf_margin_left
            ]);

        }else {

            $pdf = new Mpdf([
                'margin_top' => $pdf_margin_top,
                'margin_right' => $pdf_margin_right,
                'margin_bottom' => $pdf_margin_bottom,
                'margin_left' => $pdf_margin_left
            ]);

        }

        $path_css = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.$base_pdf_template.DIRECTORY_SEPARATOR.'style.css');
        $stylesheet = file_get_contents($path_css);

        $pdf = $this->addPageRecords($data['documents_01'], $template, $base_pdf_template, $company, $pdf, $stylesheet, $format_pdf, 'invoice', $configuration);

        $pdf = $this->addPageRecords($data['documents_03'], $template, $base_pdf_template, $company, $pdf, $stylesheet, $format_pdf, 'invoice', $configuration);

        $pdf = $this->addPageRecords($data['dispatches'], $template, $base_pdf_template, $company, $pdf, $stylesheet, $format_pdf, 'dispatch', $configuration);

        $pdf = $this->addPageRecords($data['sale_notes'], $template, $base_pdf_template, $company, $pdf, $stylesheet, $format_pdf, 'sale_note', $configuration);

        return $pdf->output('', 'S');

    }


    public function addPageRecords($documents, $template, $base_pdf_template, $company, $pdf, $stylesheet, $format_pdf, $type, $configuration){

        foreach ($documents as $document) {

            $html = $template->pdf($base_pdf_template, $type, $company, $document, $format_pdf);
            $html_footer_legend = "";

            if(config('tenant.pdf_template_footer')) {

                switch ($type) {

                    case 'invoice':
                        $html_footer = $template->pdfFooter($base_pdf_template, $document);
                        if($configuration->legend_footer){
                            $html_footer_legend = $template->pdfFooterLegend($base_pdf_template, $document);
                        }
                        $pdf->SetHTMLFooter($html_footer.$html_footer_legend);
                        break;

                    case 'dispatch':
                        $html_footer = $template->pdfFooter($base_pdf_template, $document);
                        $pdf->SetHTMLFooter($html_footer.$html_footer_legend);
                        break;

                    case 'sale_note':
                        $html_footer = ($base_pdf_template != 'full_height') ? $template->pdfFooter($base_pdf_template, $document) : $template->pdfFooter('default',$document);
                        $pdf->SetHTMLFooter($html_footer);
                        break;

                }

            }

            $pdf = $this->nextPage($pdf, $stylesheet, $html);

        }

        return $pdf;

    }


    public function nextPage($pdf, $stylesheet, $html){

        $pdf->AddPage();
        $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        return $pdf;
    }




}
