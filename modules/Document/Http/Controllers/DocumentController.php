<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentItem;
use Modules\Document\Http\Resources\DocumentNotSentCollection;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Tenant\Person;
use App\Models\Tenant\StateType;
use App\Models\Tenant\Catalogs\DetractionType;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\PaymentMethodType as CatPaymentMethodType;
use App\Traits\OfflineTrait;
use Modules\Inventory\Models\Warehouse as ModuleWarehouse;
use App\Models\Tenant\Item;
use Modules\Document\Traits\SearchTrait;
use Modules\Finance\Helpers\UploadFileHelper;
use Modules\Document\Helpers\ConsultCdr;
use Modules\Item\Models\ItemLot;
use Modules\Document\Http\Resources\ItemLotCollection;


class DocumentController extends Controller
{
    use OfflineTrait, SearchTrait;

    public function index()
    {

        $is_client = $this->getIsClient();

        return view('document::documents.not_sent', compact('is_client'));
    }

    public function records(Request $request)
    {

        $records = $this->getRecords($request);

        return new DocumentNotSentCollection($records->paginate(config('tenant.items_per_page')));

    }

    public function getRecords($request){


        $d_end = $request->d_end;
        $d_start = $request->d_start;
        $date_of_issue = $request->date_of_issue;
        $document_type_id = $request->document_type_id;
        $number = $request->number;
        $series = $request->series;
        $state_type_id = $request->state_type_id;
        $pending_payment = ($request->pending_payment == "true") ? true:false;
        $customer_id = $request->customer_id;


        if($d_start && $d_end){

            $records = Document::where('document_type_id', 'like', '%' . $document_type_id . '%')
                            ->where('series', 'like', '%' . $series . '%')
                            ->where('number', 'like', '%' . $number . '%')
                            ->where('state_type_id', 'like', '%' . $state_type_id . '%')
                            ->whereBetween('date_of_issue', [$d_start , $d_end])
                            ->whereNotSent()
                            ->whereTypeUser()
                            ->latest();

        }else{

            $records = Document::where('date_of_issue', 'like', '%' . $date_of_issue . '%')
                            ->where('document_type_id', 'like', '%' . $document_type_id . '%')
                            ->where('state_type_id', 'like', '%' . $state_type_id . '%')
                            ->where('series', 'like', '%' . $series . '%')
                            ->where('number', 'like', '%' . $number . '%')
                            ->whereNotSent()
                            ->whereTypeUser()
                            ->latest();
        }

        if($pending_payment){
            $records = $records->where('total_canceled', false);
        }

        if($customer_id){
            $records = $records->where('customer_id', $customer_id);
        }

        return $records;

    }

    public function data_table()
    {

        $customers = Person::whereType('customers')->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });

        $document_types = DocumentType::whereIn('id', ['01', '03','07', '08'])->get();
        $series = Series::whereIn('document_type_id', ['01', '03','07', '08'])->get();
        $establishments = Establishment::where('id', auth()->user()->establishment_id)->get();
        $state_types = StateType::get();

        return compact( 'customers', 'document_types','series','establishments', 'state_types');

    }



    public function upload(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,gif,svg');

        if(!$validate_upload['success']){
            return $validate_upload;
        }

        if ($request->hasFile('file')) {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return $this->upload_image($new_request);
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    function upload_image($request)
    {
        $file = $request['file'];
        $type = $request['type'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }


    public function detractionTables()
    {

        $cat_payment_method_types = CatPaymentMethodType::whereActive()->get();
        $detraction_types = DetractionType::whereActive()->get();

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

        return compact( 'detraction_types', 'cat_payment_method_types', 'locations');

    }


    public function dataTableCustomers(Request $request)
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
                                ];
                            });

        return compact('customers');
    }



    public function savePayConstancy(Request $request)
    {
        $document = Document::findOrFail($request->id);

        $detraction = $document->detraction;
        $detraction->pay_constancy = $request->pay_constancy;


        if($request->upload_image_pay_constancy){
            //hacer proceso de carga de imagen
            $image_pay_constancy = $request->upload_image_pay_constancy;
            $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'image_detractions'.DIRECTORY_SEPARATOR;

            $file_name_old = $image_pay_constancy['image'];
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($image_pay_constancy['temp_path']);
            $datenow = date('YmdHis');
            $file_name =  $detraction->detraction_type_id.'-'.$detraction->bank_account.'-'.$datenow.'.'.$file_name_old_array[1];
            Storage::put($directory.$file_name, $file_content);
            $set_image_pay_constancy = $file_name;
            $detraction->image_pay_constancy = $set_image_pay_constancy;

        }

        // dd($detraction, $request->upload_image_pay_constancy['temp_path']);
        $document->detraction = $detraction;
        $document->save();

        return [
            'success' => true,
            'message' =>  'Constancia de pago guardada',
        ];
    }


    public function prepayments($type)
    {

        $prepayment_documents = Document::whereHasPrepayment()->whereAffectationTypePrepayment($type)->get()->transform(function($row) {

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


    public function searchItems(Request $request)
    {

        $establishment_id = auth()->user()->establishment_id;
        $warehouse = ModuleWarehouse::where('establishment_id', $establishment_id)->first();

        $items_not_services = $this->getItemsNotServices($request);
        $items_services = $this->getItemsServices($request);
        $all_items = $items_not_services->merge($items_services);

        $items = collect($all_items)->transform(function($row) use($warehouse){

                $detail = $this->getFullDescription($row, $warehouse);

                return [
                    'id' => $row->id,
                    'full_description' => $detail['full_description'],
                    'brand' => $detail['brand'],
                    'category' => $detail['category'],
                    'warehouse_description' => $detail['warehouse_description'],
                    'stock' => $detail['stock'],
                    'barcode' => $row->barcode,
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
                            'warehouse_description' => $row->getWarehouseDescription(),
                            'stock' => (!empty($row->stock))?$row->stock:0,
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
                    // 'lots' => $row->item_lots->where('has_sale', false)->where('warehouse_id', $warehouse->id)->transform(function($row) {
                    //     return [
                    //         'id' => $row->id,
                    //         'series' => $row->series,
                    //         'date' => $row->date,
                    //         'item_id' => $row->item_id,
                    //         'warehouse_id' => $row->warehouse_id,
                    //         'has_sale' => (bool)$row->has_sale,
                    //         'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
                    //     ];
                    // }),
                    'lots_enabled' => (bool) $row->lots_enabled,
                    'series_enabled' => (bool) $row->series_enabled,
                    'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,
                    'model' => $row->model,
                ];
            });

        return compact('items');

    }


    public function searchLots(Request $request)
    {

        $warehouse = ModuleWarehouse::select('id')->where('establishment_id', auth()->user()->establishment_id)->first();

        if($request->document_item_id){

            //proccess credit note
            $document_item = DocumentItem::findOrFail($request->document_item_id);

            $records = ItemLot::where('series','like', "%{$request->input}%")
                                ->whereIn('id', collect($document_item->item->lots)->pluck('id')->toArray())
                                ->where('has_sale', true)
                                ->latest();

        }else{

            $records = ItemLot::where('series','like', "%{$request->input}%")
                                    ->where('item_id', $request->item_id)
                                    ->where('has_sale', false)
                                    ->where('warehouse_id', $warehouse->id)
                                    ->latest();
        }


        return new ItemLotCollection($records->paginate(config('tenant.items_per_page')));

    }


    public function regularizeLots(Request $request)
    {

        $document_item = DocumentItem::findOrFail($request->document_item_id);

        return ItemLot::where('series','like', "%{$request->input}%")
                                        ->whereIn('id', collect($document_item->item->lots)->pluck('id')->toArray())
                                        ->where('has_sale', true)
                                        ->get();


    }


    public function searchItemById($id)
    {

        $establishment_id = auth()->user()->establishment_id;
        $warehouse = ModuleWarehouse::where('establishment_id', $establishment_id)->first();

        $search_item = $this->getItemsNotServicesById($id);

        if(count($search_item) == 0){
            $search_item = $this->getItemsServicesById($id);
        }

        $items = collect($search_item)->transform(function($row) use($warehouse){

            $detail = $this->getFullDescription($row, $warehouse);

            return [
                'id' => $row->id,
                'full_description' => $detail['full_description'],
                'brand' => $detail['brand'],
                'category' => $detail['category'],
                'stock' => $detail['stock'],
                'internal_id' => $row->internal_id,
                'description' => $row->description,
                'currency_type_id' => $row->currency_type_id,
                'currency_type_symbol' => $row->currency_type->symbol,
                'sale_unit_price' => round($row->sale_unit_price, 2),
                'purchase_unit_price' => $row->purchase_unit_price,
                'unit_type_id' => $row->unit_type_id,
                'sale_affectation_igv_type_id' => $row->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $row->purchase_affectation_igv_type_id,
                'calculate_quantity' => (bool) $row->calculate_quantity,
                'has_igv' => (bool) $row->has_igv,
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
                'lots' => $row->item_lots->where('has_sale', false)->where('warehouse_id', $warehouse->id)->transform(function($row) {
                    return [
                        'id' => $row->id,
                        'series' => $row->series,
                        'date' => $row->date,
                        'item_id' => $row->item_id,
                        'warehouse_id' => $row->warehouse_id,
                        'has_sale' => (bool)$row->has_sale,
                        'lot_code' => ($row->item_loteable_type) ? (isset($row->item_loteable->lot_code) ? $row->item_loteable->lot_code:null):null
                    ];
                }),
                'lots_enabled' => (bool) $row->lots_enabled,
                'series_enabled' => (bool) $row->series_enabled,
                'has_plastic_bag_taxes' => (bool) $row->has_plastic_bag_taxes,

            ];
        });

        return compact('items');
    }


    public function consultCdr($document_id)
    {

        $document = Document::find($document_id);

        return (new ConsultCdr)->search($document);

    }

}
