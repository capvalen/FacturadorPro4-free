<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Template;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\SaleNotePaymentRequest;
use App\Http\Resources\Tenant\SaleNotePaymentCollection;
use App\Models\Tenant\Company;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\SaleNotePayment;
use FontLib\Table\Type\loca;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Modules\Finance\Traits\FinanceTrait;
use Modules\Finance\Traits\FilePaymentTrait;
use Illuminate\Support\Facades\DB;

class SaleNotePaymentController extends Controller
{
    use StorageDocument, FinanceTrait, FilePaymentTrait;

    public function records($sale_note_id)
    {
        $records = SaleNotePayment::where('sale_note_id', $sale_note_id)->get();

        return new SaleNotePaymentCollection($records);
    }

    public function tables()
    {
        return [
            'payment_method_types' => PaymentMethodType::all(),
            'payment_destinations' => $this->getPaymentDestinations()
        ];
    }

    public function document($sale_note_id)
    {
        $sale_note = SaleNote::find($sale_note_id);

        $total_paid = round(collect($sale_note->payments)->sum('payment'), 2);
        $total = $sale_note->total;
        $total_difference = round($total - $total_paid, 2);

        if($total_difference < 1)
        {
            $sale_note->total_canceled = true;
            $sale_note->save();
        }

        return [
            'number_full' => $sale_note->identifier,
            'total_paid' => $total_paid,
            'total' => $total,
            'total_difference' => $total_difference,
            'paid' => $sale_note->total_canceled
        ];
    }

    public function store(SaleNotePaymentRequest $request)
    {
        $id = $request->input('id');

        DB::connection('tenant')->transaction(function () use ($id, $request) {

            $record = SaleNotePayment::firstOrNew(['id' => $id]);
            $record->fill($request->all());
            $record->save();
            $this->createGlobalPayment($record, $request->all());
            $this->saveFiles($record, $request, 'sale_notes');

        });

        if($request->paid == true)
        {
            $sale_note = SaleNote::find($request->sale_note_id);
            $sale_note->total_canceled = true;
            $sale_note->save();
        }

        $this->createPdf($request->input('sale_note_id'));

        return [
            'success' => true,
            'message' => ($id)?'Pago editado con éxito':'Pago registrado con éxito'
        ];
    }

    public function destroy($id)
    {
        $item = SaleNotePayment::findOrFail($id);
        $sale_note_id = $item->sale_note_id;
        $item->delete();

        $sale_note = SaleNote::find($item->sale_note_id);
        $sale_note->total_canceled = false;
        $sale_note->save();

        $this->createPdf($sale_note_id);

        return [
            'success' => true,
            'message' => 'Pago eliminado con éxito'
        ];
    }

    public function createPdf($sale_note_id, $format = null)
    {
        $sale_note = SaleNote::find($sale_note_id);
        $total_paid = round(collect($sale_note->payments)->sum('payment'), 2);
        $total = $sale_note->total;
        $total_difference = round($total - $total_paid, 2);

        if($total_difference == 0) {
            Log::info('true '.$total_difference);
            $sale_note->total_canceled = true;
        } else {
            Log::info('false '.$total_difference);
            $sale_note->total_canceled = false;
        }
        $sale_note->save();


        $company = Company::first();

        $template = new Template();
        $pdf = null;
        if($format == 'a5')
        {
              $pdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => [
                    78,
                    220
                    ],
                'margin_top' => 2,
                'margin_right' => 5,
                'margin_bottom' => 0,
                'margin_left' => 5
            ]);

        }
        else{
           $pdf = new Mpdf();
        }

        $document = SaleNote::find($sale_note_id);

        $base_template = config('tenant.pdf_template');

        $html = $template->pdf($base_template, "sale_note", $company, $document,"a4");

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
                        DIRECTORY_SEPARATOR.$base_template.
                        DIRECTORY_SEPARATOR.'font')
                ]),
                'fontdata' => $fontData + [
                        'custom_bold' => [
                            'R' => $pdf_font_bold.'.ttf',
                        ],
                        'custom_regular' => [
                            'R' => $pdf_font_regular.'.ttf',
                        ],
                    ]
            ]);
        }

        $path_css = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.
            DIRECTORY_SEPARATOR.'pdf'.
            DIRECTORY_SEPARATOR.$base_template.
            DIRECTORY_SEPARATOR.'style.css');

        $stylesheet = file_get_contents($path_css);

        $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

        if(config('tenant.pdf_template_footer')) {
            $html_footer = $template->pdfFooter($base_template,$document);
            $pdf->SetHTMLFooter($html_footer);
        }

        $this->uploadStorage($document->filename, $pdf->output('', 'S'), 'sale_note');
        return $document->filename;
//        $this->uploadFile($pdf->output('', 'S'), 'sale_note');
    }

    public function toPrint($sale_note_id, $format)
    {
        $filename = $this->createPdf($sale_note_id, $format);
        $temp = tempnam(sys_get_temp_dir(), 'sale_note');
        file_put_contents($temp, $this->getStorage($filename, 'sale_note'));
        return response()->file($temp);

    }


}
