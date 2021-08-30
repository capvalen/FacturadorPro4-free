<?php
namespace Modules\Order\Http\Controllers;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Modules\Order\Http\Resources\OrderFormCollection;
use Modules\Order\Http\Resources\OrderFormResource;
use App\Http\Controllers\Controller;
use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\Catalogs\{
    IdentityDocumentType,
    TransferReasonType,
    TransportModeType,
    Department,
    Province,
    District,
    UnitType,
    Country
};
use Illuminate\Http\Request;
use App\Models\Tenant\{
    Establishment,
    Dispatch,
    Person,
    Series,
    Item
};
use Modules\Order\Http\Requests\OrderFormRequest;
use Exception, Illuminate\Support\Facades\DB;
use Modules\Order\Models\OrderForm;
use Modules\Order\Models\Dispatcher;
use Modules\Order\Models\Driver;
use Modules\Order\Helpers\OrderFormHelper;
use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Modules\Order\Mail\OrderFormEmail;
use Illuminate\Support\Facades\Mail;
use App\CoreFacturalo\Helpers\QrCode\QrCodeGenerate;


class OrderFormController extends Controller
{
    use StorageDocument;

    protected $company;
    protected $order_form;

    public function index() {
        return view('order::order_forms.index');
    }

    public function columns() {
        return [
            'date_of_issue' => 'Fecha de emisión',
            'id' => 'Número'
        ];
    }

    public function records(Request $request) {

        $records = OrderForm::where($request->column, 'like', "%{$request->value}%")
                            ->latest();

        return new OrderFormCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function create($id = null)
    {
        return view('order::order_forms.form', compact('id'));
    }

    public function dispatchCreate($order_form_id)
    {
        return view('order::order_forms.create', compact('order_form_id'));
    }


    public function record($id)
    {
        $record = new OrderFormResource(OrderForm::findOrFail($id));

        return $record;
    }

    public function store(OrderFormRequest $request) {

        DB::connection('tenant')->transaction(function () use($request) {

            $data = OrderFormHelper::set($request->all());
            // dd($data);

            $this->order_form =  OrderForm::updateOrCreate(['id' => $request->input('id')], $data);

            $this->order_form->items()->delete();

            foreach ($data['items'] as $row) {
                $this->order_form->items()->create($row);
            }

            $this->setFilename();
            $this->updateQr($request->url);
            $this->createPdf($this->order_form, "a4", $this->order_form->filename);

        });


        return [
            'success' => true,
            'data' => [
                'id' => $this->order_form->id,
            ],
        ];
    }

    /**
     * Tables
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function tables(Request $request) {

        $items = Item::query()
            ->where('item_type_id', '01')
            ->orderBy('description')
            ->get()
            ->transform(function($row) {
                $full_description = ($row->internal_id) ? $row->internal_id.' - '.$row->description : $row->description;

                return [
                    'id' => $row->id,
                    'full_description' => $full_description,
                    'description' => $row->description,
                    'internal_id' => $row->internal_id,
                    'currency_type_id' => $row->currency_type_id,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => $row->sale_unit_price,
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id
                ];
            });

        $identities = ['6', '1'];

        $customers = Person::query()
            ->whereIn('identity_document_type_id', $identities)
            ->whereType('customers')
            ->orderBy('name')
            ->whereIsEnabled()
            ->get()
            ->transform(function($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number.' - '.$row->name,
                    'name' => $row->name,
                    'trade_name' => $row->trade_name,
                    'country_id' => $row->country_id,
                    'address' => $row->address,
                    'email' => $row->email,
                    'telephone' => $row->telephone,
                    'number' => $row->number,
                    'district_id' => $row->district_id,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_code' => $row->identity_document_type->code
                ];
            });


        $locations = [];
        $departments = Department::whereActive()->get();
        foreach ($departments as $department)
        {
            $children_provinces = [];
            foreach ($department->provinces as $province)
            {
                $children_districts = [];
                foreach ($province->districts as $district)
                {
                    $children_districts[] = [
                        'value' => $district->id,
                        'label' => $district->description
                    ];
                }
                $children_provinces[] = [
                    'value' => $province->id,
                    'label' => $province->description,
                    'children' => $children_districts
                ];
            }
            $locations[] = [
                'value' => $department->id,
                'label' => $department->description,
                'children' => $children_provinces
            ];
        }


        $identityDocumentTypes = IdentityDocumentType::whereActive()->get();
        $transferReasonTypes = TransferReasonType::whereActive()->get();
        $transportModeTypes = TransportModeType::whereActive()->get();
        $departments = Department::whereActive()->get();
        $provinces = Province::whereActive()->get();
        $unitTypes = UnitType::whereActive()->get();
        $countries = Country::whereActive()->get();
        $districts = District::whereActive()->get();
        $establishments = Establishment::all();

        $drivers = Driver::query()->orderBy('name')->get()
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'description' => $row->number.' - '.$row->name,
                                    ];
                                });

        $dispatchers = Dispatcher::query()->orderBy('name')->get()
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'description' => $row->number.' - '.$row->name,
                                    ];
                                });

        return compact('establishments', 'customers', 'transportModeTypes', 'transferReasonTypes', 'unitTypes',
                            'countries', 'departments', 'provinces', 'districts', 'identityDocumentTypes', 'items',
                            'locations', 'dispatchers', 'drivers');
    }


    public function table($table){


        if($table == 'drivers'){

            return Driver::query()->orderBy('name')->get()
                            ->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                ];
                            });

        }

        if($table == 'dispatchers'){

            return Dispatcher::query()->orderBy('name')->get()
                                ->transform(function($row) {
                                    return [
                                        'id' => $row->id,
                                        'description' => $row->number.' - '.$row->name,
                                    ];
                                });

        }

        return [];

    }


    public function email(Request $request)
    {
        $company = Company::active();
        $record = OrderForm::find($request->input('id'));
        $customer_email = $request->input('customer_email');

        Configuration::setConfigSmtpMail();
        Mail::to($customer_email)->send(new OrderFormEmail($company, $record));

        return [
            'success' => true
        ];
    }

    private function setFilename(){

        $name = [$this->order_form->prefix,$this->order_form->id,date('Ymd')];
        $this->order_form->filename = join('-', $name);
        $this->order_form->save();

    }

    public function download($external_id, $format) {

        $order_form = OrderForm::where('external_id', $external_id)->first();

        if (!$order_form) throw new Exception("El código {$external_id} es inválido, no se encontro el registro relacionado");

        $this->reloadPDF($order_form, $format, $order_form->filename);

        return $this->downloadStorage($order_form->filename, 'order_form');

    }

    public function toPrint($external_id, $format) {

        $order_form = OrderForm::where('external_id', $external_id)->first();

        if (!$order_form) throw new Exception("El código {$external_id} es inválido, no se encontro el registro relacionado");

        $this->reloadPDF($order_form, $format, $order_form->filename);
        $temp = tempnam(sys_get_temp_dir(), 'order_form');

        file_put_contents($temp, $this->getStorage($order_form->filename, 'order_form'));

        return response()->file($temp);
    }


    private function reloadPDF($order_form, $format, $filename) {
        $this->createPdf($order_form, $format, $filename);
    }

    public function createPdf($order_form = null, $format_pdf = null, $filename = null) {

        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $this->company = ($this->company != null) ? $this->company : Company::active();
        $this->document = ($order_form != null) ? $order_form : $this->order_form;

        $this->configuration = Configuration::first();
        $configuration = $this->configuration->formats;
        $base_template = $configuration;

        $html = $template->pdf($base_template, "order_form", $this->company, $this->document, $format_pdf);

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

        $this->uploadFile($this->document->filename, $pdf->output('', 'S'), 'order_form');
    }

    public function uploadFile($filename, $file_content, $file_type)
    {
        $this->uploadStorage($filename, $file_content, $file_type);
    }
    public function updateQr($url)
    {
        $qrCode = new QrCodeGenerate();
        $qr = $qrCode->displayPNGBase64("{$url}/order-forms/print/{$this->order_form->external_id}/a4");
        $this->order_form->update([
            'qr' => $qr,
        ]);
    }

}
