<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Item;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Company;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Str;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\Template;
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;
use Modules\Inventory\Models\Devolution;
use Modules\Inventory\Models\DevolutionReason;
use Modules\Inventory\Models\DevolutionItem;
use Modules\Inventory\Http\Resources\DevolutionCollection;
use Modules\Inventory\Http\Resources\DevolutionResource;
use Modules\Inventory\Http\Requests\DevolutionRequest;
use App\Models\Tenant\Configuration; 


class DevolutionController extends Controller
{

    use StorageDocument;

    protected $devolution;
    protected $company;

    public function index()
    {
        return view('inventory::devolutions.index');
    }


    public function create()
    {
        return view('inventory::devolutions.form');
    }

    public function columns()
    {
        return [
            'date_of_issue' => 'Fecha de emisión',
        ];
    }

    public function records(Request $request)
    {

        $records = Devolution::where($request->column, 'like', "%{$request->value}%")
                                ->whereTypeUser()
                                ->latest();

        return new DevolutionCollection($records->paginate(config('tenant.items_per_page')));
    }
 
    public function tables() {

        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();
        $devolution_reasons = DevolutionReason::get();
        $company = Company::active();

        return compact('establishments', 'devolution_reasons', 'company');
    }
 

    public function item_tables() {

        $items = $this->table('items');
        
        return compact('items');
    }

    public function record($id)
    {
        $record = new DevolutionResource(Devolution::findOrFail($id));
        return $record;
    }

    public function getFullDescription($row){

        $desc = ($row->internal_id)?$row->internal_id.' - '.$row->description : $row->description;
        $category = ($row->category) ? " - {$row->category->name}" : "";
        $brand = ($row->brand) ? " - {$row->brand->name}" : "";

        $desc = "{$desc} {$category} {$brand}";

        return $desc;
    }

    public function store(DevolutionRequest $request) {

        DB::connection('tenant')->transaction(function () use ($request) {

            $data = $this->mergeData($request);

            $this->devolution =  Devolution::create($data);

            foreach ($data['items'] as $row) {
                
                $this->devolution->items()->create([
                    'item_id' => $row['item_id'],
                    'item' => [
                        'description' => trim($row['item']['description']),
                        'internal_id' => $row['item']['internal_id'],
                        'unit_type_id' => $row['item']['unit_type_id'],
                        'lot_selected' => isset($row['item']['lot_selected']) ? $row['item']['lot_selected'] : null,
                        'lots' => self::lots($row),
                        'IdLoteSelected' => ( isset($row['IdLoteSelected']) ? $row['IdLoteSelected'] : null )
                    ],
                    'quantity' => $row['quantity'],
                ]);
            }

            $this->setFilename();
            $this->createPdf($this->devolution, "a4", $this->devolution->filename);

        });

        return [
            'success' => true,
            'message' => 'Devolución registrada correctamente',
            'data' => [
                'id' => $this->devolution->id,
            ],
        ];
    }
 
    private static function lots($row)
    {
        if(isset($row['item']['lots']))
        {
            return $row['item']['lots'];
        }
        else if(isset($row['lots']))
        {
            return  $row['lots'];
        }
        else{
            return [];
        }
    }

    public function mergeData($inputs)
    {

        $this->company = Company::active();

        $values = [
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'soap_type_id' => $this->company->soap_type_id,
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
    }



    private function setFilename(){

        $name = [$this->devolution->prefix,$this->devolution->id,date('Ymd')];
        $this->devolution->filename = join('-', $name);
        $this->devolution->save();

    }

    public function searchItems(Request $request)
    {
        
        $query_items = Item::where('description','like', "%{$request->input}%")
                            ->orWhere('internal_id','like', "%{$request->input}%") 
                            ->whereWarehouse()
                            ->whereNotIsSet()
                            ->whereIsActive()
                            ->orderBy('description')
                            ->get();

        $items = collect($query_items)->transform(function($row){

            return [
                'id' => $row->id,
                'full_description' => $this->getFullDescription($row),
                'internal_id' => $row->internal_id,
                'description' => $row->description,
                'unit_type_id' => $row->unit_type_id,
                'lots_group' => collect($row->lots_group)->transform(function($row){
                    return [
                        'id'  => $row->id,
                        'code' => $row->code,
                        'quantity' => $row->quantity,
                        'date_of_due' => $row->date_of_due,
                        'checked'  => false
                    ];
                }),
                'lots' => [], 
                'lots_enabled' => (bool) $row->lots_enabled,
                'series_enabled' => (bool) $row->series_enabled,

            ];
        });

        return compact('items');

    }


    public function table($table)
    {
        switch ($table) { 

            case 'items':

                $items = Item::whereWarehouse()->whereIsActive()->whereNotIsSet()->orderBy('description')->take(20)->get();
    
                return collect($items)->transform(function($row){

                    return [
                        'id' => $row->id,
                        'full_description' => $this->getFullDescription($row),
                        'internal_id' => $row->internal_id,
                        'description' => $row->description,
                        'unit_type_id' => $row->unit_type_id,
                        'lots_group' => collect($row->lots_group)->transform(function($row){
                            return [
                                'id'  => $row->id,
                                'code' => $row->code,
                                'quantity' => $row->quantity,
                                'date_of_due' => $row->date_of_due,
                                'checked'  => false
                            ];
                        }),
                        'lots' => [], 
                        'lots_enabled' => (bool) $row->lots_enabled,
                        'series_enabled' => (bool) $row->series_enabled,
    
                    ];
                });

                break;
            default:
                return [];

                break;
        }
    }
 

    public function download($external_id, $format) {

        $devolution = Devolution::where('external_id', $external_id)->first();

        if (!$devolution) throw new Exception("El código {$external_id} es inválido, no se encontro el documento relacionado");

        return $this->downloadStorage($devolution->filename, 'devolution');
    }

 
    public function createPdf($devolution = null, $format_pdf = null, $filename = null) {

        ini_set("pcre.backtrack_limit", "5000000");
        $template = new Template();
        $pdf = new Mpdf();

        $document = ($devolution != null) ? $devolution : $this->devolution;
        $company = ($this->company != null) ? $this->company : Company::active();
        $filename = ($filename != null) ? $filename : $this->devolution->filename;

        // $base_template = config('tenant.pdf_template');
        $base_template = Configuration::first()->formats;
        
        $html = $template->pdf($base_template, "devolution", $company, $document, $format_pdf);

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
                $html_footer = $template->pdfFooter($base_template,$this->devolution);
                $pdf->SetHTMLFooter($html_footer);
            }
        }

        $this->uploadFile($filename, $pdf->output('', 'S'), 'devolution');
    }

    public function uploadFile($filename, $file_content, $file_type) {
        $this->uploadStorage($filename, $file_content, $file_type);
    }
 
}
