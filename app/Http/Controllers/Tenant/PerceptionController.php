<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\PerceptionRequest;
use App\Http\Resources\Tenant\PerceptionCollection;
use App\Http\Resources\Tenant\PerceptionResource;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Perception;
use App\Models\Tenant\PerceptionDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Person;
use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\Catalogs\PerceptionType;

class PerceptionController extends Controller
{

    public function __construct()
    {
        $this->middleware('input.request:perception,web', ['only' => ['store']]);
    }

    public function index()
    {
        return view('tenant.perceptions.index');
    }

    public function columns()
    {
        return [
            // 'id' => 'Código',
            'number' => 'Número'
        ];
    }

    public function records(Request $request)
    {
        $records = Perception::where($request->column, 'like', "%{$request->value}%")
                            ->orderBy('series')
                            ->orderBy('number', 'desc');

        return new PerceptionCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create()
    {
        return view('tenant.perceptions.form');
    }

    public function tables()
    {

        $perception_types = PerceptionType::get();
        $currency_types = CurrencyType::all();
        $customers = $this->table('customers');
        $items = $this->table('items');
        $company = Company::with(['identity_document_type'])->first();
        $document_types = DocumentType::all();
        $series = Series::all();
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();// Establishment::all();

        return compact('user_id', 'currency_types', 'customers', 'items', 'company', 'establishments','document_types', 'series', 'perception_types');
    }
 
    public function document_tables()
    {
        $perception_types = PerceptionType::get();
        $currency_types = CurrencyType::whereActive()->get();
        $document_types = DocumentType::whereIn('id', ['01', '03'])->get();

        return compact('document_types', 'currency_types', 'perception_types');
    }

    public function table($table)
    {
        if ($table === 'customers') {
            $customers = Person::whereType('customers')->whereIsEnabled()->with(['identity_document_type'])->orderBy('name')->get()->transform(function($row) {
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
        }
        if ($table === 'items') {
            return PerceptionDocument::all();
        }

        return [];
    }

    public function record($id)
    {
        $record = new PerceptionResource(Perception::findOrFail($id));

        return $record;
    }
 

    public function store(PerceptionRequest $request)
    {

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
            'message' => "Se generó la percepción {$document->series}-{$document->number}"
        ];

    }

    public function destroy($id)
    {
        $record = Perception::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Percepción eliminada con éxito'
        ];
    }
}