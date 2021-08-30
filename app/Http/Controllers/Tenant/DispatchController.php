<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Configuration;
use Exception;
use App\Models\Tenant\Item;
use Illuminate\Http\Request;
use App\Models\Tenant\Person;
use App\Models\Tenant\Series;
use App\Models\Tenant\Company;
use App\Models\Tenant\Dispatch;
use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;
use App\CoreFacturalo\Facturalo;
use App\Models\Tenant\Quotation;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\OrderNote;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Establishment;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Mail\DispatchEmail;
use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;
use App\Models\Tenant\Catalogs\UnitType;
use App\Models\Tenant\PaymentMethodType;
use Modules\Document\Traits\SearchTrait;
use Modules\Finance\Traits\FinanceTrait;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Requests\Tenant\DispatchRequest;
use App\Http\Resources\Tenant\DispatchCollection;
use App\Models\Tenant\Catalogs\TransportModeType;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\TransferReasonType;
use Modules\Order\Http\Resources\DispatchResource;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Models\Tenant\DispatchItem;
use Modules\Inventory\Models\Warehouse as ModuleWarehouse;

class DispatchController extends Controller
{
    use StorageDocument, FinanceTrait, SearchTrait;

    public function __construct()
    {
        $this->middleware('input.request:dispatch,web', ['only' => ['store']]);
    }

    public function index()
    {
        return view('tenant.dispatches.index');
    }

    public function columns()
    {
        return [
            'number' => 'Número'
        ];
    }

    public function records(Request $request)
    {
        $records = Dispatch::where($request->column, 'like', "%{$request->value}%")
            ->orderBy('series')
            ->orderBy('number', 'desc');

        return new DispatchCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function create($document_id = null, $type = null, $dispatch_id = null)
    {
        if ($type == 'q') {
            $document = Quotation::find($document_id);
        } elseif ($type == 'on') {
            $document = OrderNote::find($document_id);
        } else {
            $type = 'i';
            $document = Document::find($document_id);
        }

        if (!$document) {
            return view('tenant.dispatches.create');
        }

        $dispatch = Dispatch::find($dispatch_id);
        $sale_note = null;

        return view('tenant.dispatches.form', compact('document', 'type', 'dispatch', 'sale_note'));
    }

    public function generate($sale_note_id)
    {
        $sale_note = SaleNote::findOrFail($sale_note_id);
        $type = null;
        $document = $sale_note;
        $dispatch = null;

        return view('tenant.dispatches.form', compact('document', 'type', 'dispatch', 'sale_note'));
    }

    public function store(DispatchRequest $request)
    {
        if ($request->series[0] == 'T') {
            $fact = DB::connection('tenant')->transaction(function () use ($request) {
                $facturalo = new Facturalo();
                $facturalo->save($request->all());
                $facturalo->createXmlUnsigned();
                $facturalo->signXmlUnsigned();
                $facturalo->createPdf();
                $facturalo->senderXmlSignedBill();

                return $facturalo;
            });

            $document = $fact->getDocument();
            // $response = $fact->getResponse();
        } else {
            $fact = DB::connection('tenant')->transaction(function () use ($request) {
                $facturalo = new Facturalo();
                $facturalo->save($request->all());
                $facturalo->createPdf();

                return $facturalo;
            });

            $document = $fact->getDocument();
            // $response = $fact->getResponse();
        }

        return [
            'success' => true,
            'message' => "Se creo la guía de remisión {$document->series}-{$document->number}",
            'data'    => [
                'id' => $document->id,
            ],
        ];
    }

    /**
     * Tables
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function tables(Request $request)
    {
        $itemsFromSummary = null;
        if ($request->itemIds) {
            $itemsFromSummary = Item::query()
            ->with('lots_group')
            ->whereIn('id', $request->itemIds)
            ->where('item_type_id', '01')
            ->orderBy('description')
            ->get()
            ->transform(function ($row) {
                $full_description = ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description;

                return [
                    'id'                               => $row->id,
                    'full_description'                 => $full_description,
                    'description'                      => $row->description,
                    'model'                            => $row->model,
                    'internal_id'                      => $row->internal_id,
                    'currency_type_id'                 => $row->currency_type_id,
                    'currency_type_symbol'             => $row->currency_type->symbol,
                    'sale_unit_price'                  => $row->sale_unit_price,
                    'purchase_unit_price'              => $row->purchase_unit_price,
                    'unit_type_id'                     => $row->unit_type_id,
                    'sale_affectation_igv_type_id'     => $row->sale_affectation_igv_type_id,
                    'attributes'                       => $row->attributes ? $row->attributes : [],
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'has_igv'                          => $row->has_igv,
                    'lots_group' => $row->lots_group->each(function($lot){
                        return [
                            'id'  => $lot->id,
                            'code' => $lot->code,
                            'quantity' => $lot->quantity,
                            'date_of_due' => $lot->date_of_due,
                            'checked'  => false
                        ];
                    }),
                    'lots' => [],
                    'lots_enabled' => (bool) $row->lots_enabled,
                ];
            });
        }
        $items = Item::query()
            ->with('lots_group')
            ->where('item_type_id', '01')
            ->orderBy('description')
            ->take(20)
            ->get()
            ->transform(function ($row) {
                $full_description = ($row->internal_id) ? $row->internal_id . ' - ' . $row->description : $row->description;

                return [
                    'id'                               => $row->id,
                    'full_description'                 => $full_description,
                    'description'                      => $row->description,
                    'model'                            => $row->model,
                    'internal_id'                      => $row->internal_id,
                    'currency_type_id'                 => $row->currency_type_id,
                    'currency_type_symbol'             => $row->currency_type->symbol,
                    'sale_unit_price'                  => $row->sale_unit_price,
                    'purchase_unit_price'              => $row->purchase_unit_price,
                    'unit_type_id'                     => $row->unit_type_id,
                    'sale_affectation_igv_type_id'     => $row->sale_affectation_igv_type_id,
                    'attributes'                       => $row->attributes ? $row->attributes : [],
                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                    'has_igv'                          => $row->has_igv,
                    'lots_group' => $row->lots_group->each(function($lot){
                        return [
                            'id'  => $lot->id,
                            'code' => $lot->code,
                            'quantity' => $lot->quantity,
                            'date_of_due' => $lot->date_of_due,
                            'checked'  => false
                        ];
                    }),
                    'lots' => [],
                    'lots_enabled' => (bool) $row->lots_enabled,
                ];
            });

        $identities = ['6', '1'];

        // $dni_filter = config('tenant.document_type_03_filter');
        // if($dni_filter){
        //     array_push($identities, '1');
        // }

        $customers = Person::with('addresses')
            ->whereIn('identity_document_type_id', $identities)
            ->whereType('customers')
            ->orderBy('name')
            ->whereIsEnabled()
            ->get()
            ->transform(function ($row) {
                return [
                    'id'                          => $row->id,
                    'description'                 => $row->number . ' - ' . $row->name,
                    'name'                        => $row->name,
                    'trade_name'                  => $row->trade_name,
                    'country_id'                  => $row->country_id,
                    'address'                     => $row->address,
                    'addresses'                   => $row->addresses,
                    'email'                       => $row->email,
                    'telephone'                   => $row->telephone,
                    'number'                      => $row->number,
                    'district_id'                 => $row->district_id,
                    'department_id'               => $row->department_id,
                    'province_id'                 => $row->province_id,
                    'identity_document_type_id'   => $row->identity_document_type_id,
                    'identity_document_type_code' => $row->identity_document_type->code
                ];
            });

        $locations = [];
        $departments = Department::whereActive()->get();
        foreach ($departments as $department) {
            $children_provinces = [];
            foreach ($department->provinces as $province) {
                $children_districts = [];
                foreach ($province->districts as $district) {
                    $children_districts[] = [
                        'value' => $district->id,
                        'label' => $district->description
                    ];
                }
                $children_provinces[] = [
                    'value'    => $province->id,
                    'label'    => $province->description,
                    'children' => $children_districts
                ];
            }
            $locations[] = [
                'value'    => $department->id,
                'label'    => $department->description,
                'children' => $children_provinces
            ];
        }

        $identityDocumentTypes = IdentityDocumentType::whereActive()->get();
        $transferReasonTypes = TransferReasonType::whereActive()->get();
        $transportModeTypes = TransportModeType::whereActive()->get();
        $unitTypes = UnitType::whereActive()->get()->toArray();
        $countries = Country::whereActive()->get()->toArray();
        $establishments = Establishment::all();
        $series = Series::all()->toArray();
        $company = Company::select('number')->first();

        // ya se tiene un locations con lo siguiente combinado
        // $departments = Department::whereActive()->get();
        // $provinces = Province::whereActive()->get();
        // $districts = District::whereActive()->get();

        return compact(
            'establishments',
            'customers',
            'series',
            'transportModeTypes',
            'transferReasonTypes',
            'unitTypes',
            'countries',
            // 'departments',
            // 'provinces',
            // 'districts',
            'identityDocumentTypes',
            'items',
            'locations',
            'company',
            'itemsFromSummary'
        );
    }

    public function downloadExternal($type, $external_id)
    {
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

    public function record($id)
    {
        $record = new DispatchResource(Dispatch::findOrFail($id));

        return $record;
    }

    public function email(Request $request)
    {
        $record = Dispatch::find($request->input('id'));
        $customer_email = $request->input('customer_email');
        Configuration::setConfigSmtpMail();
        Mail::to($customer_email)->send(new DispatchEmail($record));

        return [
            'success' => true
        ];
    }

    public function generateDocumentTables($id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $establishment_id = $establishment->id;
        $warehouse = ModuleWarehouse::where('establishment_id', $establishment_id)->first();

        $itemsId = $dispatch->items->pluck('item_id')->all();
        $items = Item::whereIn('id', $itemsId)->get()->transform(function ($row) use ($warehouse) {
            $detail = $this->getFullDescription($row, $warehouse);
            return [
                'id'                               => $row->id,
                'full_description'                 => $detail['full_description'],
                'model'                            => $row->model,
                'brand'                            => $detail['brand'],
                'category'                         => $detail['category'],
                'stock'                            => $detail['stock'],
                'internal_id'                      => $row->internal_id,
                'description'                      => $row->description,
                'currency_type_id'                 => $row->currency_type_id,
                'currency_type_symbol'             => $row->currency_type->symbol,
                'sale_unit_price'                  => number_format($row->sale_unit_price, 4, '.', ''),
                'purchase_unit_price'              => $row->purchase_unit_price,
                'unit_type_id'                     => $row->unit_type_id,
                'sale_affectation_igv_type_id'     => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity'               => (bool) $row->calculate_quantity,
                'has_igv'                          => (bool) $row->has_igv,
                'has_plastic_bag_taxes'            => (bool) $row->has_plastic_bag_taxes,
                'amount_plastic_bag_taxes'         => $row->amount_plastic_bag_taxes,
                'item_unit_types'                  => collect($row->item_unit_types)->transform(function ($row) {
                    return [
                        'id'            => $row->id,
                        'description'   => "{$row->description}",
                        'item_id'       => $row->item_id,
                        'unit_type_id'  => $row->unit_type_id,
                        'quantity_unit' => $row->quantity_unit,
                        'price1'        => $row->price1,
                        'price2'        => $row->price2,
                        'price3'        => $row->price3,
                        'price_default' => $row->price_default,
                    ];
                }),
                'warehouses' => collect($row->warehouses)->transform(function ($row) use ($warehouse) {
                    return [
                        'warehouse_description' => $row->warehouse->description,
                        'stock'                 => $row->stock,
                        'warehouse_id'          => $row->warehouse_id,
                        'checked'               => ($row->warehouse_id == $warehouse->id) ? true : false,
                    ];
                }),
                'attributes' => $row->attributes ? $row->attributes : [],
                'lots_group' => collect($row->lots_group)->transform(function ($row) {
                    return [
                        'id'          => $row->id,
                        'code'        => $row->code,
                        'quantity'    => $row->quantity,
                        'date_of_due' => $row->date_of_due,
                        'checked'     => false
                    ];
                }),
                'lots'           => [],
                'lots_enabled'   => (bool) $row->lots_enabled,
                'series_enabled' => (bool) $row->series_enabled,
            ];
        });

        $series = Series::where('establishment_id', $establishment->id)->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', '80'])->get();
        $payment_method_types = PaymentMethodType::all();
        $payment_destinations = $this->getPaymentDestinations();
        $affectation_igv_types = AffectationIgvType::whereActive()->get();

        return response()->json([
            'dispatch'               => $dispatch,
            'document_types_invoice' => $document_types_invoice,
            'establishments'         => $establishment,
            'payment_destinations'   => $payment_destinations,
            'series'                 => $series,
            'success'                => true,
            'payment_method_types'   => $payment_method_types,
            'items'                  => $items,
            'affectation_igv_types'                  => $affectation_igv_types,
        ], 200);
    }

    public function setDocumentId($id)
    {
        request()->validate(['document_id' => 'required|exists:tenant.documents,id']);
        DB::connection('tenant')->beginTransaction();
        try {
            Dispatch::where('id', $id)
                ->update([
                    'reference_document_id' => request('document_id')
                ]);

            $dispatch = Dispatch::findOrFail($id);
            $facturalo = new Facturalo();
            $facturalo->createPdf($dispatch, 'dispatch', 'a4');

            DB::connection('tenant')->commit();
            return response()->json([
                'success' => true,
                'message' => 'Información actualiza'
            ], 200);
        } catch (\Throwable $th) {
            DB::connection('tenant')->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al asociar la guía con el comprobante. Detalles: ' . $th->getMessage()
            ], 500);
        }

    }

    public function dispatchesByClient($clientId)
    {
        $records = Dispatch::without(['user', 'soap_type', 'state_type', 'document_type', 'unit_type', 'transport_mode_type',
        'transfer_reason_type', 'items', 'reference_document'])
            ->select('series', 'number', 'id', 'date_of_issue')
            ->where('customer_id', $clientId)
            ->whereNull('reference_document_id')
            ->orderBy('series')
            ->orderBy('number', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records,
        ], 200);
    }

    public function getItemsFromDispatches(Request $request)
    {
        $request->validate([
            'dispatches_id' => 'required|array',
        ]);

        $items = DispatchItem::whereIn('dispatch_id', $request->dispatches_id)
            ->select('item_id', 'quantity')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ], 200);
    }
}
