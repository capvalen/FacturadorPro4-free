<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Item;
use App\Models\Tenant\Person;
use App\Models\Tenant\Catalogs\AffectationIgvType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use App\Models\Tenant\PaymentMethodType;
use App\Models\Tenant\CardBrand;
use App\Models\Tenant\Catalogs\CurrencyType;
use App\Models\Tenant\User;
use Modules\Inventory\Models\Warehouse;
use App\Models\Tenant\Cash;
use App\Models\Tenant\Configuration;
use Modules\Inventory\Models\InventoryConfiguration;
use Modules\Inventory\Models\ItemWarehouse;
use Exception;
use Modules\Item\Models\Category;
use Modules\Finance\Traits\FinanceTrait;
use App\Models\Tenant\Company;
use Modules\BusinessTurn\Models\BusinessTurn;
use App\Http\Resources\Tenant\PosCollection;


class PosController extends Controller
{

    use FinanceTrait;

    public function index()
    {
        $cash = Cash::where([['user_id', auth()->user()->id],['state', true]])->first();

        if(!$cash) return redirect()->route('tenant.cash.index');

        $configuration = Configuration::first();

        $company = Company::select('soap_type_id')->first();
        $soap_company  = $company->soap_type_id;
        $business_turns = BusinessTurn::select('active')->where('id', 4)->first();

        return view('tenant.pos.index', compact('configuration', 'soap_company', 'business_turns'));
    }

    public function index_full()
    {
        $cash = Cash::where([['user_id', auth()->user()->id],['state', true]])->first();

        if(!$cash) return redirect()->route('tenant.cash.index');

        return view('tenant.pos.index_full');
    }

    public function search_items(Request $request)
    {
        $configuration =  Configuration::first();

        $items = Item::where('description','like', "%{$request->input_item}%")
                            // ->orWhere('internal_id','like', "%{$request->input_item}%")
                            ->orWhere(function ($query) use ($request) {
                                $query->where('internal_id','like', "%{$request->input_item}%")
                                    ->orWhere('barcode', "{$request->input_item}");
                            })
                            ->orWhereHas('category', function($query) use($request) {
                                $query->where('name', 'like', '%' . $request->input_item . '%');
                            })
                            ->orWhereHas('brand', function($query) use($request) {
                                $query->where('name', 'like', '%' . $request->input_item . '%');
                            })
                            ->whereWarehouse()
                            ->whereIsActive()
                            ->get()->transform(function($row) use($configuration){
                                $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;
                                return [
                                    'id' => $row->id,
                                    'item_id' => $row->id,
                                    'full_description' => $full_description,
                                    'description' => $row->description,
                                    'currency_type_id' => $row->currency_type_id,
                                    'internal_id' => $row->internal_id,
                                    'currency_type_symbol' => $row->currency_type->symbol,
                                    'sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'purchase_unit_price' => $row->purchase_unit_price,
                                    'unit_type_id' => $row->unit_type_id,
                                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                                    'calculate_quantity' => (bool) $row->calculate_quantity,
                                    'is_set' => (bool) $row->is_set,
                                    'edit_unit_price' => false,
                                    'has_igv' => (bool) $row->has_igv,
                                    'aux_quantity' => 1,
                                    'aux_sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'edit_sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'image_url' => ($row->image !== 'imagen-no-disponible.jpg') ? asset('storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR.$row->image) : asset("/logo/{$row->image}"),
                                    'sets' => collect($row->sets)->transform(function($r){
                                        return [
                                            $r->individual_item->description
                                        ];
                                    }),
                                    'warehouses' => collect($row->warehouses)->transform(function ($row) {
                                        return [
                                            'warehouse_description' => $row->warehouse->description,
                                            'stock' => $row->stock,
                                        ];
                                    }),
                                    'unit_type' => $row->item_unit_types,
                                    'category' => ($row->category) ? $row->category->name : null,
                                    'brand' => ($row->brand) ? $row->brand->name : null,
                                    'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,
                                    'amount_plastic_bag_taxes' => $row->amount_plastic_bag_taxes,
                                ];
                            });

        return compact('items');

    }

    public function tables()
    {
        $affectation_igv_types = AffectationIgvType::whereActive()->get();
        $establishment = Establishment::where('id', auth()->user()->establishment_id)->first();
        $currency_types = CurrencyType::whereActive()->get();

        $customers = $this->table('customers');
        $user = User::findOrFail(auth()->user()->id);

        $items = $this->table('items');

        $categories = Category::all();

        return compact('items', 'customers','affectation_igv_types','establishment','user','currency_types', 'categories');

    }

    public function payment_tables(){

        $series = Series::whereIn('document_type_id',['01','03','80'])
                        ->where([['establishment_id', auth()->user()->establishment_id],['contingency',false]])
                        ->get();

        $payment_method_types = PaymentMethodType::all();
        $cards_brand = CardBrand::all();
        $payment_destinations = $this->getPaymentDestinations();


        return compact('series','payment_method_types','cards_brand', 'payment_destinations');

    }

    public function table($table)
    {
        if ($table === 'customers') {
            $customers = Person::whereType('customers')->whereIsEnabled()->orderBy('name')->get()->transform(function($row) {
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

            $configuration =  Configuration::first();

            $items = Item::whereWarehouse()->whereIsActive()->where('unit_type_id', '!=', 'ZZ')->where('series_enabled', 0)->orderBy('description')->take(100)
                            ->get()->transform(function($row) use ($configuration) {
                                $full_description = ($row->internal_id)?$row->internal_id.' - '.$row->description:$row->description;
                                return [
                                    'id' => $row->id,
                                    'item_id' => $row->id,
                                    'full_description' => $full_description,
                                    'description' => $row->description,
                                    'currency_type_id' => $row->currency_type_id,
                                    'internal_id' => $row->internal_id,
                                    'currency_type_symbol' => $row->currency_type->symbol,
                                    'sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'purchase_unit_price' => $row->purchase_unit_price,
                                    'unit_type_id' => $row->unit_type_id,
                                    'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                                    'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                                    'calculate_quantity' => (bool) $row->calculate_quantity,
                                    'has_igv' => (bool) $row->has_igv,
                                    'is_set' => (bool) $row->is_set,
                                    'edit_unit_price' => false,
                                    'aux_quantity' => 1,
                                    'edit_sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'aux_sale_unit_price' => number_format($row->sale_unit_price, $configuration->decimal_quantity, ".",""),
                                    'image_url' => ($row->image !== 'imagen-no-disponible.jpg') ? asset('storage'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR.$row->image) : asset("/logo/{$row->image}"),
                                    'warehouses' => collect($row->warehouses)->transform(function($row) {
                                        return [
                                            'warehouse_description' => $row->warehouse->description,
                                            'stock' => $row->stock,
                                        ];
                                    }),
                                    'category_id' => ($row->category) ? $row->category->id : null,
                                    'sets' => collect($row->sets)->transform(function($r){
                                        return [
                                            $r->individual_item->description
                                        ];
                                    }),
                                    'unit_type' => $row->item_unit_types,
                                    'category' => ($row->category) ? $row->category->name : null,
                                    'brand' => ($row->brand) ? $row->brand->name : null,
                                    'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,
                                    'amount_plastic_bag_taxes' => $row->amount_plastic_bag_taxes,

                                ];
                            });
            return $items;
        }


        if ($table === 'card_brands') {

            $card_brands = CardBrand::all();
            return $card_brands;

        }

        return [];
    }

    public function payment()
    {
        return view('tenant.pos.payment');
    }

    public function status_configuration(){

        $configuration = Configuration::first();

        return $configuration;
    }

    public function validate_stock($item_id, $quantity){

        $inventory_configuration = InventoryConfiguration::firstOrFail();
        $warehouse = Warehouse::where('establishment_id', auth()->user()->establishment_id)->first();
        $item_warehouse = ItemWarehouse::where([['item_id',$item_id], ['warehouse_id',$warehouse->id]])->first();
        $item = Item::findOrFail($item_id);

        if($item->is_set){
            $quantity = 1 * $quantity;
            $sets = $item->sets;

            foreach ($sets as $set) {
                $individual_item = $set->individual_item;
                $individual_quantity = $set->quantity * 1;
                $total_item_quantity = $individual_quantity * $quantity;
                $item_warehouse = ItemWarehouse::where([
                        ['item_id', $individual_item->id],
                        ['warehouse_id', $warehouse->id]]
                )->first();
                if(!$item_warehouse)
                    return [
                        'success' => false,
                        'message' => "El producto seleccionado no está disponible en su almacén!"
                    ];

                $stock = $item_warehouse->stock - $total_item_quantity;


                if($item_warehouse->item->unit_type_id !== 'ZZ'){
                    if (($inventory_configuration->stock_control) && ($stock < 0)){
                        return [
                            'success' => false,
                            'message' => "El producto {$item_warehouse->item->description} registrado en el conjunto {$item->description} no tiene suficiente stock!"
                        ];
                    }
                }
                // dd($individual_item);
            }



        }else{


            if(!$item_warehouse)
                return [
                    'success' => false,
                    'message' => "El producto seleccionado no está disponible en su almacén!"
                ];

            $stock = $item_warehouse->stock - $quantity;


            if($item_warehouse->item->unit_type_id !== 'ZZ'){
                if (($inventory_configuration->stock_control) && ($stock < 0)){
                    return [
                        'success' => false,
                        'message' => "El producto {$item_warehouse->item->description} no tiene suficiente stock!"
                    ];
                }
            }

        }



        return [
            'success' => true,
            'message' => ''
        ];


    }

    /**
     * Lista inicial de items en POS
     *
     * @param Request $request
     *
     * @return PosCollection
     */
    public function item(Request $request)
    {
        $items = Item::whereWarehouse()
            ->whereIsActive()
            ->where('series_enabled', 0)
            ->orderBy('description')
            ->where('unit_type_id', '!=', 'ZZ');
        self::FilterItem($items, $request);

        return new PosCollection($items->paginate(50));

    }

    /**
     * Unificacion de los filtros de busqueda de items en POS
     * Se evalua categoria como $request->cat
     * se evalua description, internal_id del item como $request->input_item
     * se evalua name de brand y category como $request->input_item
     *
     * @param Item    $item
     * @param Request $request
     */
    public static function FilterItem(&$item, Request $request)
    {
        $whereItem = [];
        $whereExtra = [];

        if ($request->cat && !empty($request->cat)) {
            $whereItem[] = ['category_id', $request->cat];
        }

        if ($request->input_item && !empty($request->input_item)) {
            $whereItem[] = ['description', 'like', '%'.$request->input_item.'%'];
            $whereItem[] = ['internal_id', 'like', '%'.$request->input_item.'%'];
            $whereExtra[] = ['name', 'like', '%'.$request->input_item.'%'];
        }

        foreach ($whereItem as $index => $wItem) {
            if($index < 1) {
                $item->Where([$wItem]);
            }else{
                $item->orWhere([$wItem]);
            }
        }

        if (!empty($whereExtra)) {
            $item
                ->orWhereHas('brand', function ($query) use ($whereExtra) {
                $query->where($whereExtra);
            })
                ->orWhereHas('category', function ($query) use ($whereExtra) {
                $query->where($whereExtra);
            });
        }
    }

    /**
     * Se busca items al escribir en input_item desde POS
     *
     * @param Request $request
     *
     * @return PosCollection
     */
    public function search_items_cat(Request $request)
    {
        $item = Item::whereWarehouse()
            ->whereIsActive()
            ->where('series_enabled', 0);

        self::FilterItem($item, $request);
        return new PosCollection($item->paginate(50));

    }
}
