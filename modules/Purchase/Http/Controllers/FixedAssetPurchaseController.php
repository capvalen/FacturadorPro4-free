<?php

namespace Modules\Purchase\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\Catalogs\ChargeDiscountType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Catalogs\DocumentType;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\Catalogs\PriceType;
use App\Models\Tenant\Catalogs\SystemIscType;
use App\Models\Tenant\Catalogs\AttributeType;
use App\Models\Tenant\Company;
use Illuminate\Support\Str;
use App\CoreFacturalo\Requests\Inputs\Common\PersonInput;
use Carbon\Carbon;

use Modules\Purchase\Http\Requests\FixedAssetPurchaseRequest;
use Modules\Purchase\Models\FixedAssetPurchase;
use Modules\Purchase\Http\Resources\FixedAssetPurchaseCollection;
use Modules\Purchase\Http\Resources\FixedAssetPurchaseResource;
use Modules\Purchase\Models\FixedAssetItem;


class FixedAssetPurchaseController extends Controller
{


    public function index()
    {
        return view('purchase::fixed_asset_purchases.index');
    }


    public function create($id = null)
    {
        return view('purchase::fixed_asset_purchases.form', compact('id'));
    }

    public function columns()
    {
        return [
            'number' => 'Número',
            'date_of_issue' => 'Fecha de emisión',
            'date_of_due' => 'Fecha de vencimiento',
            'name' => 'Nombre proveedor',
        ];
    }

    public function records(Request $request)
    {

        $records = $this->getRecords($request);

        return new FixedAssetPurchaseCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function getRecords($request){

        switch ($request->column) {
            case 'name':
                $records = FixedAssetPurchase::whereHas('supplier', function($query) use($request){
                                return $query->where($request->column, 'like', "%{$request->value}%");
                            });

                break;
 
            default:
                $records = FixedAssetPurchase::where($request->column, 'like', "%{$request->value}%");

                break;
        }

        return $records->whereTypeUser()->latest();

    }

    public function tables()
    {
        $suppliers = $this->table('suppliers');
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $currency_types = CurrencyType::whereActive()->get();
        $document_types_invoice = DocumentType::whereIn('id', ['01', '03', 'GU75', 'NE76'])->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $company = Company::active();

        return compact('suppliers', 'establishment','currency_types', 'discount_types',
                    'charge_types', 'document_types_invoice','company');
    }

    public function item_tables()
    {

        $fixed_asset_items = $this->table('fixed_asset_items');
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $system_isc_types = SystemIscType::whereActive()->get();
        $price_types = PriceType::whereActive()->get();
        $discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();
        $charge_types = ChargeDiscountType::whereType('charge')->whereLevel('item')->get();
        $attribute_types = AttributeType::whereActive()->orderByDescription()->get();

        return compact('fixed_asset_items', 'affectation_igv_types', 'system_isc_types', 'price_types',
                        'discount_types', 'charge_types', 'attribute_types');
    }

    public function record($id)
    {

        $record = new FixedAssetPurchaseResource(FixedAssetPurchase::findOrFail($id));

        return $record;
    }


    public function store(FixedAssetPurchaseRequest $request)
    {
 
        $data = self::convert($request);

        $purchase = DB::connection('tenant')->transaction(function () use ($data, $request) {

            $doc =  FixedAssetPurchase::updateOrCreate( ['id' => $request->input('id')], $data);
            $doc->items()->delete();
            
            foreach ($data['items'] as $row)
            {
                $doc->items()->create($row); 
            }

            return $doc;

        });

        return [
            'success' => true,
            'data' => [
                'id' => $purchase->id,
                'number_full' => $purchase->number_full,
            ],
        ];
    }

 

    public function voided($id)
    {

        $obj =  FixedAssetPurchase::find($id);
        $obj->state_type_id = 11;
        $obj->save();

        return [
            'success' => true,
            'message' => 'Compra anulada con éxito'
        ];

    }

    public static function convert($inputs)
    {
        $company = Company::active();
        $values = [
            'user_id' => auth()->id(),
            'external_id' => Str::uuid()->toString(),
            'supplier' => PersonInput::set($inputs['supplier_id']),
            'soap_type_id' => $company->soap_type_id,
            'group_id' => ($inputs->document_type_id === '01') ? '01':'02',
            'state_type_id' => '01'
        ];

        $inputs->merge($values);

        return $inputs->all();
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
                        'perception_agent' => (bool) $row->perception_agent,
                        'identity_document_type_id' => $row->identity_document_type_id,
                        'identity_document_type_code' => $row->identity_document_type->code
                    ];
                });
                return $suppliers;

                break;

            case 'fixed_asset_items':

                $fixed_asset_items = FixedAssetItem::orderBy('description')->get();

                return collect($fixed_asset_items)->transform(function($row) {

                            $full_description = ($row->internal_id) ? $row->internal_id.' - '.$row->name:$row->name;

                            return [
                                'id' => $row->id,
                                'full_description' => $full_description,
                                'description' => $row->name,
                                'currency_type_id' => $row->currency_type_id,
                                'currency_type_symbol' => $row->currency_type->symbol,
                                'purchase_unit_price' => $row->purchase_unit_price,
                                'unit_type_id' => $row->unit_type_id,
                                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                            ];
                    });

                break;
            default:

                return [];

                break;
        }
    }

    public function delete($id)
    {

        try {

            DB::connection('tenant')->transaction(function () use ($id) {

                $row = FixedAssetPurchase::findOrFail($id);
                $row->delete();

            });

            return [
                'success' => true,
                'message' => 'Compra eliminada con éxito'
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    public function getPersons($type){

        $persons = Person::whereType($type)->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });

        return $persons;

    }
 

}
