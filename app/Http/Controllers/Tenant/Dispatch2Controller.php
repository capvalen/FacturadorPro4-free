<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Resources\Tenant\DispatchCollection;
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
use Exception, DB;

class Dispatch2Controller extends Controller
{
    use StorageDocument;
    
    public function __construct() {
        $this->middleware('input.request:dispatch,web', ['only' => ['store']]);
    }
    
    public function index() {
        return view('tenant.dispatches.index');
    }
    
    public function columns() {
        return [
            'number' => 'Número'
        ];
    }
    
    public function records(Request $request) {
        $records = Dispatch::where($request->column, 'like', "%{$request->value}%")
            ->orderBy('series')
            ->orderBy('number', 'desc');
        
        return new DispatchCollection($records->paginate(config('tenant.items_per_page')));
    }
    
    public function create() {
        return view('tenant.dispatches.form');
    }
    
    public function store(Request $request) {
        $fact = DB::connection('tenant')->transaction(function () use($request) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->createPdf();
            $facturalo->senderXmlSignedBill();

            return $facturalo;
        });
        
        $document = $fact->getDocument();
        $response = $fact->getResponse();
        
        return [
            'success' => true,
            'message' => "Se creo la guía de remisión {$document->series}-{$document->number}",
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
        
        $customers = Person::query()
            ->whereIn('identity_document_type_id', [6])
            ->whereType('customers')
            ->orderBy('name')
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
        
        $identityDocumentTypes = IdentityDocumentType::whereActive()->get();
        $transferReasonTypes = TransferReasonType::whereActive()->get();
        $transportModeTypes = TransportModeType::whereActive()->get();
        $departments = Department::whereActive()->get();
        $provinces = Province::whereActive()->get();
        $unitTypes = UnitType::whereActive()->get();
        $countries = Country::whereActive()->get();
        $districts = District::whereActive()->get();
        $establishments = Establishment::all();
        $series = Series::all();
        
        return compact('establishments', 'customers', 'series', 'transportModeTypes', 'transferReasonTypes', 'unitTypes', 'countries', 'departments', 'provinces', 'districts', 'identityDocumentTypes', 'items');
    }
    
    public function downloadExternal($type, $external_id) {
        $retention = Dispatch::where('external_id', $external_id)->first();
        
        if (!$retention) {
            throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");
        }
        
        switch ($type) {
            case 'pdf':
                $folder = 'pdf';
                break;
            case 'xml':
                $folder = 'signed';
                break;
            case 'cdr':
                $folder = 'cdr';
                break;
            default:
                throw new Exception('Tipo de archivo a descargar es inválido');
        }
        
        return $this->downloadStorage($retention->filename, $folder);
    }
}
