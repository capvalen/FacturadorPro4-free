<?php

namespace Modules\Sale\Http\Controllers;

use App\Models\Tenant\Cash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use Modules\Sale\Http\Resources\TechnicalServiceCollection;
use Modules\Sale\Http\Resources\TechnicalServiceResource;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use App\Models\Tenant\Configuration;
use Modules\Sale\Http\Requests\TechnicalServiceRequest;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;
use Modules\Sale\Models\TechnicalService;

class TechnicalServiceController extends Controller
{
    use StorageDocument;

    protected $technical_service;
    protected $company;

    public function index()
    {
        return view('sale::technical-services.index');
    }

    public function columns()
    {
        return [
            'id' => 'Número',
            'customer' => 'Cliente',
            'date_of_issue' => 'Fecha de emisión',
        ];
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new TechnicalServiceCollection($records->paginate(config('tenant.items_per_page')));
    }

    private function getRecords($request)
    {
        if ($request->column == 'customer') {
            $records = TechnicalService::whereHas('person', function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->value}%");
            });
        } else {
            $records = TechnicalService::where($request->column, 'like', "%{$request->value}%");
        }

        return $records->whereTypeUser()->latest();
    }

    public function searchCustomers(Request $request)
    {
        $customers = Person::where('number', 'like', "%{$request->input}%")
            ->orWhere('name', 'like', "%{$request->input}%")
            ->whereType('customers')->orderBy('name')
            ->whereIsEnabled()
            ->get()->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number . ' - ' . $row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                ];
            });

        return compact('customers');
    }

    public function tables()
    {

        $customers = $this->table('customers');

        return compact('customers');

    }

    public function record($id)
    {
        $record = new TechnicalServiceResource(TechnicalService::findOrFail($id));

        return $record;
    }


    public function store(TechnicalServiceRequest $request)
    {
        DB::connection('tenant')->transaction(function () use ($request) {

            $data = $this->mergeData($request);

            $this->technical_service = TechnicalService::updateOrCreate(['id' => $request->input('id')], $data);
            $this->setFilename();
            $this->createPdf($this->technical_service, "a4", $this->technical_service->filename);

            $cash = Cash::query()->where([['user_id',auth()->id()], ['state',true]])->first();
            $cash->cash_documents()->create([
                'technical_service_id' => $this->technical_service->id
            ]);
        });

        return [
            'success' => true,
            'message' => $request->id ? 'Servicio técnico actualizado' : 'Servicio técnico registrado'
        ];

    }


    public function mergeData($inputs)
    {

        $this->company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'customer' => PersonInput::set($inputs['customer_id']),
            'soap_type_id' => $this->company->soap_type_id,
        ];

        $inputs->merge($values);

        return $inputs->all();
    }


    private function setFilename()
    {

        $name = ['TS', $this->technical_service->id, date('Ymd')];
        $this->technical_service->filename = join('-', $name);
        $this->technical_service->save();

    }


    public function table($table)
    {
        switch ($table) {
            case 'customers':

                $customers = Person::whereType('customers')->whereIsEnabled()->orderBy('name')->take(20)->get()->transform(function ($row) {
                    return [
                        'id' => $row->id,
                        'description' => $row->number . ' - ' . $row->name,
                        'name' => $row->name,
                        'number' => $row->number,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $customers;

                break;
            default:
                return [];

                break;
        }
    }

    public function searchCustomerById($id)
    {

        $customers = Person::whereType('customers')
            ->where('id', $id)
            ->get()->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number . ' - ' . $row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_code' => $row->identity_document_type->code
                ];
            });

        return compact('customers');
    }


    public function toPrint($id, $format)
    {

        $technical_service = TechnicalService::find($id);

        if (!$technical_service) throw new Exception("El código es inválido, no se encontró el servicio técnico relacionado");

        $this->reloadPDF($technical_service, $format, $technical_service->filename);
        $temp = tempnam(sys_get_temp_dir(), 'technical_service');

        file_put_contents($temp, $this->getStorage($technical_service->filename, 'technical_service'));

        return response()->file($temp);
    }


    private function reloadPDF($technical_service, $format, $filename)
    {
        $this->createPdf($technical_service, $format, $filename);
    }

    public function createPdf($technical_service = null, $format_pdf = null, $filename = null)
    {

        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $document = ($technical_service != null) ? $technical_service : $this->technical_service;
        $company = ($this->company != null) ? $this->company : Company::active();
        $filename = ($filename != null) ? $filename : $this->technical_service->filename;

        $configuration = Configuration::first();

        $base_template = $configuration->formats; //config('tenant.pdf_template');

        $html = $template->pdf($base_template, "technical_service", $company, $document, $format_pdf);

        $pdf_font_regular = config('tenant.pdf_name_regular');
        $pdf_font_bold = config('tenant.pdf_name_bold');

        if ($pdf_font_regular != false) {
            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $default = [
                'fontDir' => array_merge($fontDirs, [
                    app_path('CoreFacturalo' . DIRECTORY_SEPARATOR . 'Templates' .
                        DIRECTORY_SEPARATOR . 'pdf' .
                        DIRECTORY_SEPARATOR . $base_template .
                        DIRECTORY_SEPARATOR . 'font')
                ]),
                'fontdata' => $fontData + [
                        'custom_bold' => [
                            'R' => $pdf_font_bold . '.ttf',
                        ],
                        'custom_regular' => [
                            'R' => $pdf_font_regular . '.ttf',
                        ],
                    ]
            ];

            if ($base_template == 'citec') {
                $default = [
                    'mode' => 'utf-8',
                    'margin_top' => 2,
                    'margin_right' => 0,
                    'margin_bottom' => 0,
                    'margin_left' => 0,
                    'fontDir' => array_merge($fontDirs, [
                        app_path('CoreFacturalo' . DIRECTORY_SEPARATOR . 'Templates' .
                            DIRECTORY_SEPARATOR . 'pdf' .
                            DIRECTORY_SEPARATOR . $base_template .
                            DIRECTORY_SEPARATOR . 'font')
                    ]),
                    'fontdata' => $fontData + [
                            'custom_bold' => [
                                'R' => $pdf_font_bold . '.ttf',
                            ],
                            'custom_regular' => [
                                'R' => $pdf_font_regular . '.ttf',
                            ],
                        ]
                ];

            }

            $pdf = new Mpdf($default);
        }

        $path_css = app_path('CoreFacturalo' . DIRECTORY_SEPARATOR . 'Templates' .
            DIRECTORY_SEPARATOR . 'pdf' .
            DIRECTORY_SEPARATOR . $base_template .
            DIRECTORY_SEPARATOR . 'style.css');

        $stylesheet = file_get_contents($path_css);

        $pdf->WriteHTML($stylesheet, HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($html, HTMLParserMode::HTML_BODY);


        $this->uploadFile($filename, $pdf->output('', 'S'), 'technical_service');
    }


    public function uploadFile($filename, $file_content, $file_type)
    {
        $this->uploadStorage($filename, $file_content, $file_type);
    }


    public function destroy($id)
    {

        $record = TechnicalService::findOrFail($id);

        if ($record->payments()->count() > 0) {
            return [
                'success' => false,
                'message' => 'El servicio técnico tiene pagos asociados, debe eliminarlos previamente'
            ];
        }

        $record->delete();

        return [
            'success' => true,
            'message' => 'Servicio técnico eliminado con éxito'
        ];

    }

}
