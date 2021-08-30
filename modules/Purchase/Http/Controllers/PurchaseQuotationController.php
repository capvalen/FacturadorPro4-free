<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Str;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;
use Illuminate\Support\Facades\Mail;
use Modules\Purchase\Models\PurchaseQuotation;
use Modules\Purchase\Http\Resources\PurchaseQuotationCollection;
use Modules\Purchase\Http\Resources\PurchaseQuotationResource;
use Modules\Purchase\Mail\PurchaseQuotationEmail;


class PurchaseQuotationController extends Controller
{

    use StorageDocument;

    protected $purchase_quotation;
    protected $company;

    public function index()
    {
        return view('purchase::purchase-quotations.index');
    }


    public function create($id = null)
    {
        return view('purchase::purchase-quotations.form', compact('id'));
    }


    public function columns()
    {
        return [
            'date_of_issue' => 'Fecha de emisión'
        ];
    }

    public function records(Request $request)
    {
        $records = PurchaseQuotation::where($request->column, 'like', "%{$request->value}%")
                            ->whereTypeUser()
                            ->latest();

        return new PurchaseQuotationCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function tables() {

        $suppliers = $this->table('suppliers');
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();
        $company = Company::active();

        return compact('suppliers', 'establishments','company');
    }


    public function item_tables()
    {

        $items = $this->table('items');

        return compact('items');
    }


    public function record($id)
    {
        $record = new PurchaseQuotationResource(PurchaseQuotation::findOrFail($id));

        return $record;
    }


    public function getFullDescription($row){

        $desc = ($row->internal_id)?$row->internal_id.' - '.$row->description : $row->description;
        $category = ($row->category) ? " - {$row->category->name}" : "";
        $brand = ($row->brand) ? " - {$row->brand->name}" : "";

        $desc = "{$desc} {$category} {$brand}";

        return $desc;
    }


    public function store(Request $request) {

        DB::connection('tenant')->transaction(function () use ($request) {
            $data = $this->mergeData($request);

            $this->purchase_quotation =  PurchaseQuotation::updateOrCreate(
                ['id' => $request->input('id')],
                $data);

            $this->purchase_quotation->items()->delete();

            foreach ($data['items'] as $row) {
                $this->purchase_quotation->items()->create($row);
            }

            $this->setFilename();
            $this->createPdf($this->purchase_quotation, "a4", $this->purchase_quotation->filename);
           // $this->email($this->purchase_quotation);
        });

        return [
            'success' => true,
            'data' => [
                'id' => $this->purchase_quotation->id,
            ],
        ];
    }


    public function mergeData($inputs)
    {

        $this->company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'establishment' => EstablishmentInput::set($inputs['establishment_id']),
            'soap_type_id' => $this->company->soap_type_id,
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
    }



    private function setFilename(){

        $name = [$this->purchase_quotation->prefix,$this->purchase_quotation->id,date('Ymd')];
        $this->purchase_quotation->filename = join('-', $name);
        $this->purchase_quotation->save();

    }


    public function table($table)
    {
        switch ($table) {
            case 'suppliers':

                $suppliers = Person::whereType('suppliers')->orderBy('name')->get()->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->number.' - '.$row->name,
                        'name' => $row->name,
                        'number' => $row->number,
                        'email' => $row->email,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $suppliers;

                break;

            case 'items':

                $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

                $items = Item::orderBy('description')->whereNotIsSet()
                    ->get()->transform(function($row) {
                    $full_description = $this->getFullDescription($row);
                    return [
                        'id' => $row->id,
                        'full_description' => $full_description,
                        'description' => $row->description,
                        'unit_type_id' => $row->unit_type_id,
                        'is_set' => (bool) $row->is_set,
                    ];
                });
                return $items;

                break;
            default:
                return [];

                break;
        }
    }


    public function download($external_id, $format = "a4") {

        $purchase_quotation = PurchaseQuotation::where('external_id', $external_id)->first();

        if (!$purchase_quotation) throw new Exception("El código {$external_id} es inválido, no se encontro la cotización de compra relacionada");

        $this->reloadPDF($purchase_quotation, $format, $purchase_quotation->filename);

        return $this->downloadStorage($purchase_quotation->filename, 'purchase_quotation');

    }

    public function toPrint($external_id, $format) {

        $purchase_quotation = PurchaseQuotation::where('external_id', $external_id)->first();

        if (!$purchase_quotation) throw new Exception("El código {$external_id} es inválido, no se encontro la cotización de compra relacionada");

        $this->reloadPDF($purchase_quotation, $format, $purchase_quotation->filename);
        $temp = tempnam(sys_get_temp_dir(), 'purchase_quotation');

        file_put_contents($temp, $this->getStorage($purchase_quotation->filename, 'purchase_quotation'));

        return response()->file($temp);

    }

    private function reloadPDF($purchase_quotation, $format, $filename) {
        $this->createPdf($purchase_quotation, $format, $filename);
    }

    public function createPdf($purchase_quotation = null, $format_pdf = null, $filename = null) {

        $template = new Template();
        $pdf = new Mpdf();

        $document = ($purchase_quotation != null) ? $purchase_quotation : $this->purchase_quotation;
        $company = ($this->company != null) ? $this->company : Company::active();
        $filename = ($filename != null) ? $filename : $this->purchase_quotation->filename;

        $base_template = config('tenant.pdf_template');

        $html = $template->pdf($base_template, "purchase_quotation", $company, $document, $format_pdf);

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

        if ($format_pdf != 'ticket') {
            if(config('tenant.pdf_template_footer')) {
                $html_footer = $template->pdfFooter($base_template,$this->purchase_quotation);
                $pdf->SetHTMLFooter($html_footer);
            }
        }

        $this->uploadFile($filename, $pdf->output('', 'S'), 'purchase_quotation');
    }

    public function uploadFile($filename, $file_content, $file_type) {
        $this->uploadStorage($filename, $file_content, $file_type);
    }


    public function email($purchase_quotation)
    {
        $suppliers = $purchase_quotation->suppliers;
        // dd($suppliers);

        foreach ($suppliers as $supplier) {

            $client = Person::find($supplier->supplier_id);
            $supplier_email = $supplier->email;

            Configuration::setConfigSmtpMail();
            Mail::to($supplier_email)->send(new PurchaseQuotationEmail($client, $purchase_quotation));
        }

        return [
            'success' => true
        ];
    }
}
