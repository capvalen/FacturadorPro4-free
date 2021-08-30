<?php
namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\PaymentCondition;
use Config;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Nexmo\Account\Price;
use App\Models\Tenant\Item;
use App\Models\Tenant\User;
use App\Traits\OfflineTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Exports\PaymentExport;
use App\Models\Tenant\Company;
use Modules\Item\Models\Brand;
use App\Models\Tenant\Document;
use App\CoreFacturalo\Facturalo;
use App\Imports\DocumentsImport;
use App\Models\Tenant\StateType;
use App\Models\Tenant\Warehouse;
use Modules\Item\Models\Category;
use App\Mail\Tenant\DocumentEmail;
use Illuminate\Support\Facades\DB;
use App\CoreFacturalo\WS\Zip\ZipFly;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant\PaymentMethodType;
use Modules\Finance\Traits\FinanceTrait;
use App\Imports\DocumentsImportTwoFormat;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\DocumentType;
use Modules\Item\Http\Requests\BrandRequest;
use App\Http\Requests\Tenant\DocumentRequest;
use App\Models\Tenant\Catalogs\AttributeType;
use App\Models\Tenant\Catalogs\NoteDebitType;
use App\Models\Tenant\Catalogs\OperationType;
use App\Models\Tenant\Catalogs\SystemIscType;
use Modules\BusinessTurn\Models\BusinessTurn;
use App\Models\Tenant\Catalogs\DetractionType;
use App\Models\Tenant\Catalogs\NoteCreditType;
use App\Http\Resources\Tenant\DocumentResource;
use Modules\Item\Http\Requests\CategoryRequest;
use App\Http\Resources\Tenant\DocumentCollection;
use App\Http\Requests\Tenant\DocumentEmailRequest;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\ChargeDiscountType;
use App\Http\Requests\Tenant\DocumentUpdateRequest;
use App\Http\Requests\Tenant\DocumentVoidedRequest;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use Modules\Inventory\Models\Warehouse as ModuleWarehouse;
use App\Models\Tenant\Catalogs\PaymentMethodType as CatPaymentMethodType;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\SaleNote;

use App\Models\Tenant\InventoryKardex;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Kardex;
use Modules\Finance\Models\GlobalPayment;

class DocumentController extends Controller
{
    use StorageDocument, OfflineTrait, FinanceTrait;
    private $max_count_payment = 0;

    public function __construct()
    {
        $this->middleware('input.request:document,web', ['only' => ['store']]);
        $this->middleware('input.request:documentUpdate,web', ['only' => ['update']]);
    }

    public function index()
    {
        $is_client = $this->getIsClient();
        $import_documents = config('tenant.import_documents');
        $import_documents_second = config('tenant.import_documents_second_format');
        $configuration = Configuration::first();

        return view('tenant.documents.index', compact('is_client','import_documents','import_documents_second','configuration'));
    }

    public function columns()
    {
        return [
            'number' => 'Número',
            'date_of_issue' => 'Fecha de emisión'
        ];
    }

    public function records(Request $request)
    {

        $records = $this->getRecords($request);

        return new DocumentCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function searchCustomers(Request $request)
    {

        //tru de boletas en env esta en true filtra a los con dni   , false a todos
        $identity_document_type_id = $this->getIdentityDocumentTypeId($request->document_type_id, $request->operation_type_id);
//        $operation_type_id_id = $this->getIdentityDocumentTypeId($request->operation_type_id);

        $customers = Person::where('number','like', "%{$request->input}%")
                            ->orWhere('name','like', "%{$request->input}%")
                            ->whereType('customers')->orderBy('name')
                            ->whereIn('identity_document_type_id',$identity_document_type_id)
                            ->whereIsEnabled()
                            ->get()->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                    'identity_document_type_code' => $row->identity_document_type->code,
                                    'addresses' => $row->addresses,
                                    'address' =>  $row->address
                                ];
                            });

        return compact('customers');
    }


    public function create()
    {
        if(auth()->user()->type == 'integrator')
            return redirect('/documents');

        $configuration = Configuration::first();
        $is_contingency = 0;
        return view('tenant.documents.form', compact('is_contingency', 'configuration'));
    }

    public function create_tensu()
    {
        if(auth()->user()->type == 'integrator')
            return redirect('/documents');

        $is_contingency = 0;
        return view('tenant.documents.form_tensu', compact('is_contingency'));
    }


    public function tables()
    {
        $customers = $this->table('customers');
        // $prepayment_documents = $this->table('prepayment_documents');
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();// Establishment::all();
        $series = collect(Series::all())->transform(function($row) {
            return [
                'id' => $row->id,
                'contingency' => (bool) $row->contingency,
                'document_type_id' => $row->document_type_id,
                'establishment_id' => $row->establishment_id,
                'number' => $row->number
            ];
        });
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03'])->get();
        $document_types_note = DocumentType::whereIn('id', ['07', '08'])->get();
        $note_credit_types = NoteCreditType::whereActive()->orderByDescription()->get();
        $note_debit_types = NoteDebitType::whereActive()->orderByDescription()->get();
        $currency_types = CurrencyType::whereActive()->get();
        $operation_types = OperationType::whereActive()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $company = Company::active();
        $document_type_03_filter = config('tenant.document_type_03_filter');
        $user = auth()->user()->type;
        $sellers = User::where('establishment_id', auth()->user()->establishment_id)->whereIn('type', ['seller'])->orWhere('id', auth()->user()->id)->get();
        $payment_method_types = $this->table('payment_method_types');
        $business_turns = BusinessTurn::where('active', true)->get();
        $enabled_discount_global = config('tenant.enabled_discount_global');
        $is_client = $this->getIsClient();
        $select_first_document_type_03 = config('tenant.select_first_document_type_03');
        $payment_conditions = PaymentCondition::all();

        $document_types_guide = DocumentType::whereIn('id', ['09', '31'])->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'active' => (bool) $row->active,
                'short' => $row->short,
                'description' => ucfirst(mb_strtolower(str_replace('REMITENTE ELECTRÓNICA','REMITENTE',$row->description))),
            ];
        });
        // $cat_payment_method_types = CatPaymentMethodType::whereActive()->get();
        // $detraction_types = DetractionType::whereActive()->get();

//        return compact('customers', 'establishments', 'series', 'document_types_invoice', 'document_types_note',
//                       'note_credit_types', 'note_debit_types', 'currency_types', 'operation_types',
//                       'discount_types', 'charge_types', 'company', 'document_type_03_filter',
//                       'document_types_guide');

        // return compact('customers', 'establishments', 'series', 'document_types_invoice', 'document_types_note',
        //                'note_credit_types', 'note_debit_types', 'currency_types', 'operation_types',
        //                'discount_types', 'charge_types', 'company', 'document_type_03_filter');

        $payment_destinations = $this->getPaymentDestinations();

        return compact( 'customers','establishments', 'series', 'document_types_invoice', 'document_types_note',
                        'note_credit_types', 'note_debit_types', 'currency_types', 'operation_types',
                        'discount_types', 'charge_types', 'company', 'document_type_03_filter',
                        'document_types_guide', 'user', 'sellers','payment_method_types','enabled_discount_global',
                        'business_turns','is_client','select_first_document_type_03', 'payment_destinations', 'payment_conditions');

    }

    public function item_tables()
    {
        $items = $this->table('items');
        $categories = [];
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $system_isc_types = SystemIscType::whereActive()->get();
        $price_types = PriceType::whereActive()->get();
        $operation_types = OperationType::whereActive()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $attribute_types = AttributeType::whereActive()->orderByDescription()->get();
        $is_client = $this->getIsClient();

        return compact('items', 'categories', 'affectation_igv_types', 'system_isc_types', 'price_types',
                       'operation_types', 'discount_types', 'charge_types', 'attribute_types','is_client');
    }

    public function table($table)
    {
        if ($table === 'customers') {
            $customers = Person::with('addresses')->whereType('customers')->whereIsEnabled()->orderBy('name')->take(20)->get()->transform(function($row) {
                return [
                    'id' => $row->id,
                    'description' => $row->number.' - '.$row->name,
                    'name' => $row->name,
                    'number' => $row->number,
                    'identity_document_type_id' => $row->identity_document_type_id,
                    'identity_document_type_code' => $row->identity_document_type->code,
                    'addresses' => $row->addresses,
                    'address' =>  $row->address,
                    'internal_code' => $row->internal_code
                ];
            });
            return $customers;
        }

        if ($table === 'prepayment_documents') {
            $prepayment_documents = Document::whereHasPrepayment()->get()->transform(function($row) {

                $total = round($row->pending_amount_prepayment, 2);
                $amount = ($row->affectation_type_prepayment == '10') ? round($total/1.18, 2) : $total;

                return [
                    'id' => $row->id,
                    'description' => $row->series.'-'.$row->number,
                    'series' => $row->series,
                    'number' => $row->number,
                    'document_type_id' => ($row->document_type_id == '01') ? '02':'03',
                    // 'amount' => $row->total_value,
                    // 'total' => $row->total,
                    'amount' => $amount,
                    'total' => $total,

                ];
            });
            return $prepayment_documents;
        }

        if ($table === 'payment_method_types') {

            $payment_method_types = PaymentMethodType::whereNotIn('id', ['05', '08', '09'])->get();
            $end_payment_method_types = PaymentMethodType::whereIn('id', ['05', '08', '09'])->get(); //by requirement

            return $payment_method_types->merge($end_payment_method_types);
        }

        if ($table === 'items') {

            $establishment_id = auth()->user()->establishment_id;
            $warehouse = ModuleWarehouse::where('establishment_id', $establishment_id)->first();
            // $items_u = Item::whereWarehouse()->whereIsActive()->whereNotIsSet()->orderBy('description')->take(20)->get();
            $items_u = Item::with('warehousePrices')
                ->whereIsActive()
                ->orderBy('description');
            $items_s = Item::with('warehousePrices')
                ->where('items.unit_type_id', 'ZZ')
                ->whereIsActive()
                ->orderBy('description');
            $items_u = $items_u
                ->take(20)
                ->get();
            $items_s = $items_s
                ->take(10)
                ->get();
            $items = $items_u->merge($items_s);

            return collect($items)->transform(function($row) use($warehouse){
                $detail = $this->getFullDescription($row, $warehouse);
                return [
                    'id' => $row->id,
                    'full_description' => $detail['full_description'],
                    'model' => $row->model,
                    'brand' => $detail['brand'],
                    'warehouse_description' => $detail['warehouse_description'],
                    'category' => $detail['category'],
                    'stock' => $detail['stock'],
                    'internal_id' => $row->internal_id,
                    'description' => $row->description,
                    'currency_type_id' => $row->currency_type_id,
                    'currency_type_symbol' => $row->currency_type->symbol,
                    'sale_unit_price' => Item::getSaleUnitPriceByWarehouse($row, $warehouse->id),
                    'purchase_unit_price' => $row->purchase_unit_price,
                    'unit_type_id' => $row->unit_type_id,
                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'calculate_quantity' => (bool) $row->calculate_quantity,
                    'has_igv' => (bool) $row->has_igv,
                    'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,
                    'amount_plastic_bag_taxes' => $row->amount_plastic_bag_taxes,
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
                    'warehouses' => collect($row->warehouses)->transform(function($row) use($warehouse){
                        return [
                            'warehouse_description' => $row->warehouse->description,
                            'stock' => $row->stock,
                            'warehouse_id' => $row->warehouse_id,
                            'checked' => ($row->warehouse_id == $warehouse->id) ? true : false,
                        ];
                    }),
                    'attributes' => $row->attributes ? $row->attributes : [],
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
        }

        return [];
    }

    public function getFullDescription($row, $warehouse){

        $desc = ($row->internal_id)?$row->internal_id.' - '.$row->description : $row->description;
        $category = ($row->category) ? "{$row->category->name}" : "";
        $brand = ($row->brand) ? "{$row->brand->name}" : "";



        if($row->unit_type_id != 'ZZ')
        {
            if(isset($row['stock'])){
                $warehouse_stock = number_format($row['stock'],2);
            } else {
                $warehouse_stock = ($row->warehouses && $warehouse) ?
                    number_format($row->warehouses->where('warehouse_id', $warehouse->id)->first()->stock,2) :
                    0;
            }

            $stock = ($row->warehouses && $warehouse) ? "{$warehouse_stock}" : "";
        }
        else{
            $stock = '';
        }

        $desc = "{$desc} - {$brand}";

        return [
            'full_description' => $desc,
            'brand' => $brand,
            'category' => $category,
            'stock' => $stock,
            'warehouse_description' => $warehouse->description,
        ];
    }


    public function record($id)
    {
        $record = new DocumentResource(Document::findOrFail($id));

        return $record;
    }

    public function store(DocumentRequest $request)
    {
        $res = $this->storeWithData($request->all());
        $document_id = $res['data']['id'];
        $this->associateDispatchesToDocument($request, $document_id);
        $this->associateSaleNoteToDocument($request, $document_id);

        return $res;
    }

    public function storeWithData($data)
    {
        $fact = DB::connection('tenant')->transaction(function () use ($data) {
            $facturalo = new Facturalo();
            $facturalo->save($data);
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->updateHash();
            $facturalo->updateQr();
            $facturalo->createPdf();
            $facturalo->senderXmlSignedBill();

            return $facturalo;
        });

        $document = $fact->getDocument();
        $response = $fact->getResponse();

        return [
          'success' => true,
          'data' => [
              'id' => $document->id,
              'response' =>$response
          ]
        ];
    }

    private function associateSaleNoteToDocument(Request $request, int $documentId)
    {
        if ($request->sale_note_id) {
            SaleNote::where('id', $request->sale_note_id)
                ->update(['document_id' => $documentId]);
        }
        $notes = $request->sale_notes_relateds;
        if ($notes) {
            foreach ($notes as $note) {
                $noteArray = explode('-', $note);
                if (count($noteArray) === 2) {
                    SaleNote::where('series', $noteArray[0])
                        ->where('number', $noteArray[1])
                        ->update(['document_id' => $documentId]);
                }
            }
        }
    }

    private function associateDispatchesToDocument(Request $request, int $documentId)
    {
        $dispatches_relateds = $request->dispatches_relateds;
        if ($dispatches_relateds) {
            foreach ($dispatches_relateds as $dispatch) {
                $dispatchToArray = explode('-', $dispatch);
                if (count($dispatchToArray) === 2) {
                    Dispatch::where('series', $dispatchToArray[0])
                        ->where('number', $dispatchToArray[1])
                        ->update([
                            'reference_document_id' => $documentId,
                        ]);

                    $document = Dispatch::where('series', $dispatchToArray[0])
                        ->where('number', $dispatchToArray[1])
                        ->first();

                    if ($document) {
                        $facturalo = new Facturalo();
                        $facturalo->createPdf($document, 'dispatch', 'a4');
                    }
                }
            }
        }
    }

    public function edit($documentId)
    {
        if(auth()->user()->type == 'integrator') {
            return redirect('/documents');
        }
        $configuration = Configuration::first();
        $is_contingency = 0;
        $isUpdate = true;
        return view('tenant.documents.form', compact('is_contingency', 'configuration', 'documentId', 'isUpdate'));
    }

    public function update(DocumentUpdateRequest $request, $id)
    {
        $fact = DB::connection('tenant')->transaction(function () use ($request, $id) {
            $facturalo = new Facturalo();
            $facturalo->update($request->all(), $id);

            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->updateHash();
            $facturalo->updateQr();
            $facturalo->createPdf();

            return $facturalo;
        });

        $document = $fact->getDocument();
        $response = $fact->getResponse();

        return [
            'success' => true,
            'data'    => [
                'id'       => $document->id,
                'response' => $response,
            ],
        ];
    }

    public function show($documentId)
    {
        $document = Document::findOrFail($documentId);
        return response()->json([
            'data' => $document,
            'success' => true,
        ], 200);
    }

    public function reStore($document_id)
    {
        $fact = DB::connection('tenant')->transaction(function () use ($document_id) {
            $document = Document::find($document_id);

            $type = 'invoice';
            if($document->document_type_id === '07') {
                $type = 'credit';
            }
            if($document->document_type_id === '08') {
                $type = 'debit';
            }

            $facturalo = new Facturalo();
            $facturalo->setDocument($document);
            $facturalo->setType($type);
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->updateHash();
            $facturalo->updateQr();
            $facturalo->updateSoap('02', $type);
            $facturalo->updateState('01');
            $facturalo->createPdf($document, $type, 'ticket');
//            $facturalo->senderXmlSignedBill();
        });

//        $document = $fact->getDocument();
//        $response = $fact->getResponse();

        return [
            'success' => true,
            'message' => 'El documento se volvio a generar.',
        ];
    }

    public function email(DocumentEmailRequest $request)
    {
        $company = Company::active();
        $document = Document::find($request->input('id'));
        $customer_email = $request->input('customer_email');
        Configuration::setConfigSmtpMail();

        Mail::to($customer_email)->send(new DocumentEmail($company, $document));

        return [
            'success' => true
        ];
    }

    public function send($document_id) {
        $document = Document::find($document_id);

        $fact = DB::connection('tenant')->transaction(function () use ($document) {
            $facturalo = new Facturalo();
            $facturalo->setDocument($document);
            $facturalo->loadXmlSigned();
            $facturalo->onlySenderXmlSignedBill();
            return $facturalo;
        });

        $response = $fact->getResponse();

        return [
            'success' => true,
            'message' => $response['description'],
        ];
    }

    public function consultCdr($document_id)
    {
        $document = Document::find($document_id);

        $fact = DB::connection('tenant')->transaction(function () use ($document) {
            $facturalo = new Facturalo();
            $facturalo->setDocument($document);
            $facturalo->consultCdr();
        });

        $response = $fact->getResponse();

        return [
            'success' => true,
            'message' => $response['description'],
        ];
    }

    public function sendServer($document_id, $query = false) {
        $document = Document::find($document_id);
        // $bearer = config('tenant.token_server');
        // $api_url = config('tenant.url_server');
        $bearer = $this->getTokenServer();
        $api_url = $this->getUrlServer();
        $client = new Client(['base_uri' => $api_url, 'verify' => false]);

       // $zipFly = new ZipFly();
        if(!$document->data_json) throw new Exception("Campo data_json nulo o inválido - Comprobante: {$document->fullnumber}");

        $data_json = (array) $document->data_json;
        $data_json['numero_documento'] = $document->number;
        $data_json['external_id'] = $document->external_id;
        $data_json['hash'] = $document->hash;
        $data_json['qr'] = $document->qr;
        $data_json['query'] = $query;
        $data_json['file_xml_signed'] = base64_encode($this->getStorage($document->filename, 'signed'));
        $data_json['file_pdf'] = base64_encode($this->getStorage($document->filename, 'pdf'));
        // dd($data_json);
        $res = $client->post('/api/documents_server', [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer '.$bearer,
                'Accept' => 'application/json',
            ],
            'form_params' => $data_json
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        if ($response['success']) {
            $document->send_server = true;
            $document->save();
        }

        return $response;
    }

    public function checkServer($document_id) {
        $document = Document::find($document_id);
        $bearer = $this->getTokenServer();
        $api_url = $this->getUrlServer();

        $client = new Client(['base_uri' => $api_url, 'verify' => false]);

        $res = $client->get('/api/document_check_server/'.$document->external_id, [
            'headers' => [
                'Authorization' => 'Bearer '.$bearer,
                'Accept' => 'application/json',
            ],
        ]);

        $response = json_decode($res->getBody()->getContents(), true);

        if ($response['success']) {
            $state_type_id = $response['state_type_id'];
            $document->state_type_id = $state_type_id;
            $document->save();

            if ($state_type_id === '05') {
                $this->uploadStorage($document->filename, base64_decode($response['file_cdr']), 'cdr');
            }
        }

        return $response;
    }

    public function searchCustomerById($id)
    {

        $customers = Person::with('addresses')->whereType('customers')
                    ->where('id',$id)
                    ->get()->transform(function($row) {
                        return [
                            'id' => $row->id,
                            'description' => $row->number.' - '.$row->name,
                            'name' => $row->name,
                            'number' => $row->number,
                            'identity_document_type_id' => $row->identity_document_type_id,
                            'identity_document_type_code' => $row->identity_document_type->code,
                            'addresses' => $row->addresses,
                            'address' =>  $row->address
                        ];
                    });

        return compact('customers');
    }

    public function getIdentityDocumentTypeId($document_type_id, $operation_type_id){

        // if($operation_type_id === '0101' || $operation_type_id === '1001') {

        if(in_array($operation_type_id, ['0101', '1001', '1004'])) {

            if($document_type_id == '01'){
                $identity_document_type_id = [6];
            }else{
                if(config('tenant.document_type_03_filter')){
                    $identity_document_type_id = [1];
                }else{
                    $identity_document_type_id = [1,4,6,7,0];
                }
            }
        } else {
            $identity_document_type_id = [1,4,6,7,0];
        }

        return $identity_document_type_id;
    }

    public function changeToRegisteredStatus($document_id)
    {
        $document = Document::find($document_id);
        if($document->state_type_id === '01') {
            $document->state_type_id = '05';
            $document->save();

            return [
                'success' => true,
                'message' => 'El estado del documento fue actualizado.',
            ];
        }
    }

    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                $import = new DocumentsImport();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' =>  __('app.actions.upload.success'),
                    'data' => $data
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' =>  $e->getMessage()
                ];
            }
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    public function importTwoFormat(Request $request)
    {
        if ($request->hasFile('file')) {
            try {
                $import = new DocumentsImportTwoFormat();
                $import->import($request->file('file'), null, Excel::XLSX);
                $data = $import->getData();
                return [
                    'success' => true,
                    'message' =>  __('app.actions.upload.success'),
                    'data' => $data
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' =>  $e->getMessage()
                ];
            }
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    public function messageLockedEmission(){

        $configuration = Configuration::first();
        // $quantity_documents = Document::count();
        $quantity_documents = $configuration->quantity_documents;

        if($configuration->limit_documents !== 0 && ($quantity_documents > $configuration->limit_documents))
            return [
                'success' => false,
                'message' => 'Alcanzó el límite permitido para la emisión de comprobantes',
            ];


        return [
            'success' => true,
            'message' => '',
        ];
    }

    public function getRecords($request){


        $d_end = $request->d_end;
        $d_start = $request->d_start;
        $date_of_issue = $request->date_of_issue;
        $document_type_id = $request->document_type_id;
        $state_type_id = $request->state_type_id;
        $number = $request->number;
        $series = $request->series;
        $pending_payment = ($request->pending_payment == "true") ? true:false;
        $customer_id = $request->customer_id;
        $item_id = $request->item_id;
        $category_id = $request->category_id;
        $purchase_order = $request->purchase_order;
        $guides = $request->guides;


        $records = Document::query();
		if ($d_start && $d_end) {
			$records = $records->whereBetween('date_of_issue', [$d_start, $d_end]);
		}
        if ($date_of_issue) {
            $records = Document::where('date_of_issue', 'like', '%' . $date_of_issue . '%');
        }
        if ($document_type_id) {
            $records = $records->where('document_type_id', 'like', '%' . $document_type_id . '%');
        }
        if ($series) {
            $records = $records->where('series', 'like', '%' . $series . '%');
        }
        if ($number) {
            $records = $records->where('number', $number);
        }
        if ($state_type_id) {
            $records = $records->where('state_type_id', 'like', '%' . $state_type_id . '%');
        }
        if ($purchase_order) {
            $records = $records->where('purchase_order', $purchase_order);
        }
        $records = $records->whereTypeUser()
            ->latest();

        if($pending_payment){
            $records = $records->where('total_canceled', false);
        }

        if($customer_id){
            $records = $records->where('customer_id', $customer_id);
        }

        if($item_id){
            $records = $records->whereHas('items', function($query) use($item_id){
                                    $query->where('item_id', $item_id);
                                });
        }

        if($category_id){

            $records = $records->whereHas('items', function($query) use($category_id){
                                    $query->whereHas('relation_item', function($q) use($category_id){
                                        $q->where('category_id', $category_id);
                                    });
                                });
        }
        if(!empty($guides)){
            $records->where('guides','like', DB::raw("%\"number\":\"%").$guides. DB::raw("%\"%"));
        }
        return $records;
    }

    public function data_table()
    {

        $customers = $this->table('customers');
        $items = $this->getItems();
        $categories = Category::orderBy('name')->get();
        $state_types = StateType::get();
        $document_types = DocumentType::whereIn('id', ['01', '03','07', '08'])->get();
        $series = Series::whereIn('document_type_id', ['01', '03','07', '08'])->get();
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();// Establishment::all();

        return compact( 'customers', 'document_types','series','establishments', 'state_types', 'items', 'categories');

    }


    public function getItems(){

        $items = Item::orderBy('description')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => ($row->internal_id) ? "{$row->internal_id} - {$row->description}" :$row->description,
            ];
        });

        return $items;

    }


    public function getDataTableItem(Request $request) {

        $items = Item::where('description','like', "%{$request->input}%")
                        ->orWhere('internal_id','like', "%{$request->input}%")
                        ->orderBy('description')
                        ->get()->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'description' => ($row->internal_id) ? "{$row->internal_id} - {$row->description}" :$row->description,
                            ];
                        });

        return $items;

    }


    private function updateMaxCountPayments($value)
    {
        if($value > $this->max_count_payment)
        {
            $this->max_count_payment = $value;
        }
       // $this->max_count_payment = 20 ;//( $value > $this->max_count_payment) ? $value : $this->$max_count_payment;
    }

    private function transformReportPayment($resource)
    {

        $records = $resource->transform(function($row) {

            $total_paid = collect($row->payments)->sum('payment');
            $total = $row->total;
            $total_difference = round($total - $total_paid, 2);

            $this->updateMaxCountPayments($row->payments->count());

            return (object)[

                'id' => $row->id,
                'ruc' => $row->customer->number,
                // 'date' =>  $row->date_of_issue->format('Y-m-d'),
                // 'date' =>  $row->date_of_issue,
                'date' =>  $row->date_of_issue->format('d/m/Y'),
                'invoice' => $row->number_full,
                'comercial_name' => $row->customer->trade_name,
                'business_name' => $row->customer->name,
                'zone' => $row->customer->department->description,
                'total' => number_format($row->total, 2, ".",""),

                'payments' => $row->payments,

                /*'payment1' =>  ( isset($row->payments[0]) ) ?  number_format($row->payments[0]->payment, 2) : '',
                'payment2' =>  ( isset($row->payments[1]) ) ?  number_format($row->payments[1]->payment, 2) : '',
                'payment3' =>   ( isset($row->payments[2]) ) ?  number_format($row->payments[2]->payment, 2) : '',
                'payment4' =>   ( isset($row->payments[3]) ) ?  number_format($row->payments[3]->payment, 2) : '', */

                'balance' => $total_difference,
                'person_type' => isset($row->person->person_type->description) ? $row->person->person_type->description:'',
                'department' => $row->customer->department->description,
                'district' => $row->customer->district->description,

                /*'reference1' => ( isset($row->payments[0]) ) ?  $row->payments[0]->reference : '',
                'reference2' =>  ( isset($row->payments[1]) ) ?  $row->payments[1]->reference : '',
                'reference3' =>  ( isset($row->payments[2]) ) ?  $row->payments[2]->reference : '',
                'reference4' =>  ( isset($row->payments[3]) ) ?  $row->payments[3]->reference : '', */
            ];
        });

        return $records;
    }

    public function report_payments(Request $request)
    {
        // $month_format = Carbon::parse($month)->format('m');

        if($request->anulled == 'true') {
           $records = Document::whereBetween('date_of_issue', [$request->date_start, $request->date_end])->get();
        } else {
            $records = Document::whereBetween('date_of_issue', [$request->date_start, $request->date_end])->where('state_type_id', '!=', '11')->get();
        }

        $source =  $this->transformReportPayment( $records );

        return (new PaymentExport)
                ->records($source)
                ->payment_count($this->max_count_payment)
                ->download('Reporte_Pagos_'.Carbon::now().'.xlsx');

    }

    public function destroyDocument($document_id)
    {
        try {

            DB::connection('tenant')->transaction(function () use ($document_id) {

                $record = Document::findOrFail($document_id);
                $this->deleteAllPayments($record->payments);
                $record->delete();

            });

            return [
                'success' => true,
                'message' => 'Documento eliminado con éxito'
            ];

        } catch (Exception $e) {

            return ($e->getCode() == '23000') ? ['success' => false,'message' => 'El Documento esta siendo usada por otros registros, no puede eliminar'] : ['success' => false,'message' => 'Error inesperado, no se pudo eliminar el Documento'];

        }


    }

    public function storeCategories(CategoryRequest $request)
    {
        $id = $request->input('id');
        $category = Category::firstOrNew(['id' => $id]);
        $category->fill($request->all());
        $category->save();


        return [
            'success' => true,
            'message' => ($id)?'Categoría editada con éxito':'Categoría registrada con éxito',
            'data' => $category

        ];
    }

    public function storeBrands(BrandRequest $request){
         $id = $request->input('id');
        $brand = Brand::firstOrNew(['id' => $id]);
        $brand->fill($request->all());
        $brand->save();


        return [
            'success' => true,
            'message' => ($id)?'Marca editada con éxito':'Marca registrada con éxito',
            'data' => $brand
        ];
    }

    public function searchExternalId(Request $request)
    {
        return response()->json(Document::where('external_id', $request->external_id)->first());
    }

    public function viewGenerarNBoletas()
    {
        $is_client = $this->getIsClient();
        $import_documents = config('tenant.import_documents');
        $import_documents_second = config('tenant.import_documents_second_format');

        return view('tenant.documents.generarNBoletas', compact('is_client','import_documents','import_documents_second'));
    }

    public function createQueryExtern()
    {
        if(auth()->user()->type == 'integrator')
            return redirect('/documents');

        $configuration = Configuration::first();
        $is_contingency = 0;
        return view('tenant.documents.form-query-extern', compact('is_contingency', 'configuration'));
    }

    public function tablesQueryExtern()
    {
        $customers = $this->table('customers');
        // $prepayment_documents = $this->table('prepayment_documents');
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();// Establishment::all();
        $series = collect(Series::all())->transform(function($row) {
            return [
                'id' => $row->id,
                'contingency' => (bool) $row->contingency,
                'document_type_id' => $row->document_type_id,
                'establishment_id' => $row->establishment_id,
                'number' => $row->number
            ];
        });
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03'])->get();
        $document_types_note = DocumentType::whereIn('id', ['07', '08'])->get();
        $note_credit_types = NoteCreditType::whereActive()->orderByDescription()->get();
        $note_debit_types = NoteDebitType::whereActive()->orderByDescription()->get();
        $currency_types = CurrencyType::whereActive()->get();
        $operation_types = OperationType::whereActive()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $company = Company::active();
        $document_type_03_filter = config('tenant.document_type_03_filter');
        $user = auth()->user()->type;
        $sellers = User::whereIn('type', ['seller'])->orWhere('id', auth()->user()->id)->get();
        $payment_method_types = $this->table('payment_method_types');
        $business_turns = BusinessTurn::where('active', true)->get();
        $enabled_discount_global = config('tenant.enabled_discount_global');
        $is_client = $this->getIsClient();
        $select_first_document_type_03 = config('tenant.select_first_document_type_03');
        $payment_conditions = PaymentCondition::all();

        $document_types_guide = DocumentType::whereIn('id', ['09', '31'])->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'active' => (bool) $row->active,
                'short' => $row->short,
                'description' => ucfirst(mb_strtolower($row->description)),
            ];
        });

        $payment_destinations = $this->getPaymentDestinations();
        return compact( 'customers','establishments', 'series', 'document_types_invoice', 'document_types_note',
                        'note_credit_types', 'note_debit_types', 'currency_types', 'operation_types',
                        'discount_types', 'charge_types', 'company', 'document_type_03_filter',
                        'document_types_guide', 'user', 'sellers','payment_method_types','enabled_discount_global',
                        'business_turns','is_client','select_first_document_type_03', 'payment_destinations', 'payment_conditions');

    }

    public function storeQueryExtern(DocumentRequest $request)
    {
        
        $numero_boletas = $request->numero_boletas;
        
        for ($x = 0; $x <= $numero_boletas; $x++) {
            $fact = DB::connection('tenant')->transaction(function () use ($request) {
                $facturalo = new Facturalo();
                $facturalo->save($request->all());
                $facturalo->createXmlUnsigned();
                $facturalo->signXmlUnsigned();
                $facturalo->updateHash();
                $facturalo->updateQr();
                $facturalo->createPdf();
                $facturalo->senderXmlSignedBill();
                return $facturalo;
            });
            $document = $fact->getDocument();
            $response = $fact->getResponse();
    
            $this->associateDispatchesToDocument($request, $document->id);
            return [
                'success' => true,
                'data' => [
                    'id' => $document->id,
                    'response' =>$response
                ],
            ];
        }
    }

    public function generarBoletas(Request $request, $id, $numero_boletas){

        for ($x = 1; $x <= $numero_boletas; $x++) {
            //encontramos el id del documento a duplicar
            DB::beginTransaction();
            try{
        
                $document = Document::where('id', '=', $id)->get()->toArray();
                $numero_aleatorio = rand(100001, 999999);
                $serie_actual= $document[0]["series"];
                $ultimo_valor_boleta = Document::where('series', '=', $serie_actual)->orderby('number','DESC')->take(1)->get();
				$document_new = [];
				foreach($document as $key => $item){
                    foreach($item as $key => $row){
                        $document_new[$key] = $row;
                    }
                }
                $filename_new_document = substr($document[0]["filename"], 0, 20);
                $external_id_new_document = substr($document[0]["external_id"], 0, 30);
                $customer_decode = $document[0]["customer"];
                $establishment_decode = $document[0]["establishment"];
                $legends_decode = $document[0]["legends"];
                $document_new["customer"] = $customer_decode;
                $document_new["external_id"] = $external_id_new_document.$numero_aleatorio;
                $document_new["establishment"] = $establishment_decode;
                $document_new["legends"] = $legends_decode;
                $document_new["state_type_id"] = "01";
                $document_new["additional_information"] = null;
                $document_new["number"] = $ultimo_valor_boleta[0]->number + 1;
                $document_new["filename"] = $filename_new_document.($ultimo_valor_boleta[0]->number + 1);
				unset($document_new["group"], $document_new["document_type"], $document_new["currency_type"], $document_new["user"], $document_new["state_type"], $document_new["invoice"], $document_new["items"], $document_new["payments"]);
				// return response()->json($document_new["customer"]);
                
				// $document_new_customer = json_encode((array) $document_new["customer"]);
                
				// $document_new_establishment = json_encode((array)$document_new["establishment"]);
				// $document_new_legends = json_encode((array)$document_new["legends"]);
				
                // $document_new["customer"] = response()->json($document_new["customer"]);
                // $document_new["establishment"] = $document_new_establishment;
                // $document_new["legends"] = $document_new_legends;
				
				
                //creamos un nuevo documento con todos campos del documento a duplicar
                $new_document = Document::create($document_new);
				
        ////////////////AQUÍ TERMINAMOS CON LA TABLA DE DOCUMENTS///////////////////////
				
        ////////////////AQUÍ EMPEZAMOS CON LA TABLA DE DOCUMENT_ITEMS///////////////////////
                //encontramos los items del documento original
                				
                
        ////////////////AQUÍ TERMINAMOS CON LA TABLA DE DOCUMENT_ITEMS///////////////////////

        ////////////////AQUÍ EMPEZAMOS CON LA TABLA DE DOCUMENT_PAYMENTS///////////////////////
				
                //encontramos el document_payment
                $document_payments = DocumentPayment::where('document_id','=', $document[0]["id"])->get()->toArray();
				unset($document_payments[0]["payment_method_type"]);
				
                $r_document_payments = [];
                foreach($document_payments as $key => $item){
                    foreach($item as $key => $row){
                        $r_document_payments[$key] = $row;
                    }
                }
                $r_document_payments["document_id"] = $new_document["id"];
                $row_document_payment = DocumentPayment::create($r_document_payments);
				
        ////////////////AQUÍ TERMINAMOS CON LA TABLA DE DOCUMENT_PAYMENTS///////////////////////

        ////////////////AQUÍ EMPEZAMOS CON LA TABLA DE GLOBAL_PAYMENTS///////////////////////
                $global_payments = GlobalPayment::where('payment_id','=', $r_document_payments["id"])->get()->toArray();
                $new_global_payments = [];
                foreach($global_payments as $key => $item){
                    foreach($item as $key => $row){
                        $new_global_payments[$key] = $row;
                    }
                }
                $new_global_payments["payment_id"] = $row_document_payment["id"];
                $row_r_document_payment = GlobalPayment::create($new_global_payments);
				
        ////////////////AQUÍ TERMINAMOS CON LA TABLA DE GLOBAL_PAYMENTS///////////////////////

                
        ////////////////AQUÍ EMPEZAMOS CON LA TABLA DE INVOICES///////////////////////
                $invoices = Invoice::where('document_id','=', $document[0]["id"])->get()->toArray();
				unset($invoices[0]["operation_type"]);
				
                $new_invoices = [];
                foreach($invoices as $key => $item){
                    foreach($item as $key => $row){
                        $new_invoices[$key] = $row;
                    }
                }
                $new_invoices["document_id"] = $new_document["id"];
                $r_new_invoices = Invoice::create($new_invoices);
				
				$document_items = DocumentItem::where('document_id','=', $document[0]["id"])->get()->toArray();
				
				unset($document_items[0]["affectation_igv_type"], $document_items[0]["price_type"]);
				
                foreach($document_items as $key => $items){
                    $new_document_items = [];
                    foreach($items as $key => $item){
                        $new_document_items[$key] = $item;
                    }
					
                    $item_decode = $new_document_items["item"];
					$additional_information = null;
					
                    $new_document_items["item"] = $item_decode;
					$new_document_items["additional_information"] = $additional_information;
                    $new_document_items["document_id"] = $new_document["id"];
					
					
                    $r_new_document_items = DocumentItem::create($new_document_items);
                }
        ////////////////AQUÍ TERMINAMOS CON LA TABLA DE KARDEX///////////////////////
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
            }
		}
		return [
			'success' => true
		];
			
    }

}
