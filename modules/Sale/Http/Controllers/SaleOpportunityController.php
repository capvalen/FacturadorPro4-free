<?php

namespace Modules\Sale\Http\Controllers;

use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\ChargeDiscountType;
use App\Models\Tenant\Establishment;
use App\CoreFacturalo\Requests\Inputs\Common\LegendInput;
use App\Models\Tenant\Item;
use App\Models\Tenant\Series;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\DocumentType;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\Catalogs\AttributeType;
use App\Models\Tenant\Company;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Str;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\CoreFacturalo\Requests\Inputs\Common\EstablishmentInput;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\Tenant\PaymentMethodType;
use Modules\Sale\Models\SaleOpportunity;
use Modules\Sale\Models\SaleOpportunityItem;
use Modules\Sale\Http\Resources\SaleOpportunityCollection;
use Modules\Sale\Http\Resources\SaleOpportunityResource;
use Modules\Sale\Http\Resources\SaleOpportunityResource2;
use Modules\Sale\Http\Requests\SaleOpportunityRequest;
use Modules\Sale\Mail\SaleOpportunityEmail;

class SaleOpportunityController extends Controller
{

    use StorageDocument;

    protected $sale_opportunity;
    protected $company;

    public function index()
    {
        return view('sale::sale_opportunities.index');
    }


    public function create($id = null)
    {
        return view('sale::sale_opportunities.form', compact('id'));
    }

    public function columns()
    {
        return [
            'date_of_issue' => 'Fecha de emisión',
            'user_name' => 'Vendedor',
            'customer_name' => 'Cliente'
        ];
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new SaleOpportunityCollection($records->paginate(config('tenant.items_per_page')));
    }

    private function getRecords($request){

        if($request->column == 'user_name'){

            $records = SaleOpportunity::whereHas('user', function($query) use($request){
                            $query->where('name', 'like', "%{$request->value}%");
                        })
                        ->whereTypeUser()
                        ->latest();

        }elseif($request->column == 'customer_name'){

            $records = SaleOpportunity::whereHas('person', function($query) use($request){
                            $query->where('name', 'like', "%{$request->value}%");
                        })
                        ->whereTypeUser()
                        ->latest();

        }else{

            $records = SaleOpportunity::where($request->column, 'like', "%{$request->value}%")
                                ->whereTypeUser()
                                ->latest();

        }

        return $records;
    }

    public function searchCustomers(Request $request)
    {

        $customers = Person::where('number','like', "%{$request->input}%")
                            ->orWhere('name','like', "%{$request->input}%")
                            ->whereType('customers')->orderBy('name')
                            ->get()->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                    'identity_document_type_code' => $row->identity_document_type->code
                                ];
                            });

        return compact('customers');
    }

    public function tables() {

        $customers = $this->table('customers');
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();
        $currency_types = CurrencyType::whereActive()->get();
        $company = Company::active();

        return compact('customers', 'establishments','currency_types','company');
    }


    public function option_tables()
    {
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $series = Series::where('establishment_id',$establishment->id)->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = []; //$this->getPaymentDestinations();

        return compact('series', 'document_types_invoice', 'payment_method_types', 'payment_destinations');
    }


    public function item_tables() {

        $items = $this->table('items');
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $price_types = PriceType::whereActive()->get();

        return compact('items', 'affectation_igv_types', 'price_types');
    }


    public function record($id)
    {
        $record = new SaleOpportunityResource(SaleOpportunity::findOrFail($id));

        return $record;
    }


    public function getFullDescription($row){

        $desc = ($row->internal_id)?$row->internal_id.' - '.$row->description : $row->description;
        $category = ($row->category) ? " - {$row->category->name}" : "";
        $brand = ($row->brand) ? " - {$row->brand->name}" : "";

        $desc = "{$desc} {$category} {$brand}";

        return $desc;
    }


    public function store(SaleOpportunityRequest $request) {

        // dd($request->all());

        DB::connection('tenant')->transaction(function () use ($request) {

            $data = $this->mergeData($request);

            $this->sale_opportunity =  SaleOpportunity::updateOrCreate( ['id' => $request->input('id')], $data);

            $this->sale_opportunity->items()->delete();
            $this->sale_opportunity->files()->delete();

            foreach ($data['items'] as $row) {
                $this->sale_opportunity->items()->create($row);
            }

            app(SaleOpportunityFileController::class)->saveFiles($this->sale_opportunity, $data['files']);

            $this->setFilename();
            $this->createPdf($this->sale_opportunity, "a4", $this->sale_opportunity->filename);

        });

        return [
            'success' => true,
            'data' => [
                'id' => $this->sale_opportunity->id,
            ],
        ];
    }


    public function mergeData($inputs)
    {

        $this->company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'external_id' => ($inputs->id) ? $inputs->external_id : Str::uuid()->toString(),
            'customer' => PersonInput::set($inputs['customer_id']),
            'establishment' => EstablishmentInput::set($inputs['establishment_id']),
            'soap_type_id' => $this->company->soap_type_id,
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
    }



    private function setFilename(){

        $name = [$this->sale_opportunity->prefix,$this->sale_opportunity->id,date('Ymd')];
        $this->sale_opportunity->filename = join('-', $name);
        $this->sale_opportunity->save();

    }


    public function table($table)
    {
        switch ($table) {
            case 'customers':

                $customers = Person::whereType('customers')->orderBy('name')->take(20)->get()->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->number.' - '.$row->name,
                        'name' => $row->name,
                        'number' => $row->number,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $customers;

                break;

            case 'items':

                $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();

                $items = Item::orderBy('description')->whereIsActive()->whereNotIsSet()
                    // ->with(['warehouses' => function($query) use($warehouse){
                    //     return $query->where('warehouse_id', $warehouse->id);
                    // }])
                    ->get()->transform(function($row) {
                    $full_description = $this->getFullDescription($row);
                    // $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;
                    return [
                        'id' => $row->id,
                        'full_description' => $full_description,
                        'description' => $row->description,
                        'currency_type_id' => $row->currency_type_id,
                        'currency_type_symbol' => $row->currency_type->symbol,
                        'sale_unit_price' => $row->sale_unit_price,
                        'purchase_unit_price' => $row->purchase_unit_price,
                        'unit_type_id' => $row->unit_type_id,
                        'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                        'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                        'is_set' => (bool) $row->is_set,
                        'has_igv' => (bool) $row->has_igv,
                        'calculate_quantity' => (bool) $row->calculate_quantity,
                        'item_unit_types' => collect($row->item_unit_types)->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'description' => "{$row->description}",
                                'item_id' => $row->item_id,
                                'unit_type_id' => $row->unit_type_id,
                                'quantity_unit' => $row->quantity_unit,
                                'price1' => $row->price1,
                                'price2' => $row->price2,
                                'price3' => $row->price3,
                                'price_default' => $row->price_default,
                            ];
                        }),
                        'warehouses' => collect($row->warehouses)->transform(function($row) {
                            return [
                                'warehouse_id' => $row->warehouse->id,
                                'warehouse_description' => $row->warehouse->description,
                                'stock' => $row->stock,
                            ];
                        })
                    ];
                });
                return $items;

                break;
            default:
                return [];

                break;
        }
    }

    public function searchCustomerById($id)
    {

        $customers = Person::whereType('customers')
                    ->where('id',$id)
                    ->get()->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->number.' - '.$row->name,
                            'name' => $row->name,
                            'number' => $row->number,
                            'identity_document_type_id' => $row->identity_document_type_id,
                            'identity_document_type_code' => $row->identity_document_type->code
                        ];
                    });

        return compact('customers');
    }

    public function download($external_id, $format = 'a4') {
        $sale_opportunity = SaleOpportunity::where('external_id', $external_id)->first();

        if (!$sale_opportunity) throw new Exception("El código {$external_id} es inválido, no se encontro el pedido relacionado");

        $this->reloadPDF($sale_opportunity, $format, $sale_opportunity->filename);

        return $this->downloadStorage($sale_opportunity->filename, 'sale_opportunity');
    }


    public function toPrint($external_id, $format) {
        $sale_opportunity = SaleOpportunity::where('external_id', $external_id)->first();

        if (!$sale_opportunity) throw new Exception("El código {$external_id} es inválido, no se encontro el pedido relacionado");

        $this->reloadPDF($sale_opportunity, $format, $sale_opportunity->filename);
        $temp = tempnam(sys_get_temp_dir(), 'sale_opportunity');

        file_put_contents($temp, $this->getStorage($sale_opportunity->filename, 'sale_opportunity'));

        return response()->file($temp);
    }


    private function reloadPDF($sale_opportunity, $format, $filename) {
        $this->createPdf($sale_opportunity, $format, $filename);
    }

    public function createPdf($sale_opportunity = null, $format_pdf = null, $filename = null) {

        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $document = ($sale_opportunity != null) ? $sale_opportunity : $this->sale_opportunity;
        $company = ($this->company != null) ? $this->company : Company::active();
        $filename = ($filename != null) ? $filename : $this->sale_opportunity->filename;

        $base_template = Establishment::find($document->establishment_id)->template_pdf;;

        $html = $template->pdf($base_template, "sale_opportunity", $company, $document, $format_pdf);


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
                $html_footer = $template->pdfFooter($base_template,$document);
                $pdf->SetHTMLFooter($html_footer);
            }
        }

        $this->uploadFile($filename, $pdf->output('', 'S'), 'sale_opportunity');
    }

    public function uploadFile($filename, $file_content, $file_type) {
        $this->uploadStorage($filename, $file_content, $file_type);
    }

    public function email(Request $request)
    {

        $client = Person::find($request->customer_id);
        $sale_opportunity = SaleOpportunity::find($request->id);
        $customer_email = $request->input('customer_email');

        Configuration::setConfigSmtpMail();
        Mail::to($customer_email)->send(new SaleOpportunityEmail($client, $sale_opportunity));

        return [
            'success' => true
        ];
    }
}
