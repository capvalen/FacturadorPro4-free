<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\GeneralItemExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Document;
use App\Models\Tenant\PurchaseItem;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\GeneralItemCollection;
use Modules\Report\Traits\ReportTrait;


class ReportGeneralItemController extends Controller
{
    use ReportTrait;

    public function __construct()
    {
    }

    public function filter() {

        $customers = $this->getPersons('customers');
        $suppliers = $this->getPersons('suppliers');
        $items = $this->getItems('items');
        $brands = $this->getBrands();
        $web_platforms = $this->getWebPlatforms();
        $document_types = DocumentType::whereIn('id', ['01', '03', '80'])->get();
        $categories = $this->getCategories();
        $users = $this->getUsers();

        return compact('document_types', 'suppliers', 'customers', 'items','web_platforms', 'brands', 'categories', 'users');
    }


    public function index() {

        return view('report::general_items.index');
    }


    public function records(Request $request)
    {

        $records = $this->getRecordsItems($request->all())->latest('id');

        return new GeneralItemCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function getRecordsItems($request){

        $data_of_period = $this->getDataOfPeriod($request);
        $data_type = $this->getDataType($request);

        $document_type_id = $request['document_type_id'];
        $d_start = $data_of_period['d_start'];
        $d_end = $data_of_period['d_end'];

        $person_id = $request['person_id'];
        $type_person = $request['type_person'];
        $item_id = $request['item_id'];
        $brand_id = $request['brand_id'];
        $category_id = $request['category_id'];

        $user_id = $request['user_id'];
        $user_type = $request['user_type'];
        $web_platform_id = $request['web_platform_id'];

        $records = $this->dataItems($d_start, $d_end, $document_type_id, $data_type, $person_id, $type_person, $item_id, $web_platform_id, $brand_id, $category_id, $user_id, $user_type);

        return $records;

    }


    private function dataItems($date_start, $date_end, $document_type_id, $data_type, $person_id, $type_person, $item_id, $web_platform_id, $brand_id, $category_id, $user_id, $user_type)
    {
        /* columna state_type_id */
        $documents_excluded = [
            '11' // Documentos anulados
        ];
        if( $document_type_id && $document_type_id == '80' ) {
            $relation = 'sale_note';

            $data = SaleNoteItem::whereHas('sale_note', function($query) use($date_start, $date_end, $user_id, $documents_excluded){
                $query
                ->whereBetween('date_of_issue', [$date_start, $date_end])
                ->latest()
                ->whereTypeUser();
                if(!empty($user_id)){
                    $query->where('user_id',$user_id);
                }
                $query->whereNotIn('state_type_id', $documents_excluded);
            });

        } else {

            $model = $data_type['model'];
            $relation = $data_type['relation'];

            $document_types = $document_type_id ? [$document_type_id] : ['01','03'];

            $data = $model::whereHas($relation, function ($query) use ($date_start, $date_end, $document_types, $model,$documents_excluded) {
                $query
                    ->whereBetween('date_of_issue', [$date_start, $date_end])
                    ->whereIn('document_type_id', $document_types)
                    ->latest()
                    ->whereTypeUser();
                if ($model == 'App\Models\Tenant\DocumentItem') {
                    $query->whereNotIn('state_type_id', $documents_excluded);
                }
            });
            if ($user_id && $user_type === 'CREADOR') {
                $data = $data->whereHas($relation.'.user', function($query) use($user_id){
                    $query->where('user_id', $user_id);
                });
            }
			if ($user_id && $user_type === 'VENDEDOR') {
				$data = $data->whereHas($relation . '.seller', function ($query) use ($user_id) {
					$query->where('seller_id', $user_id);
				});
			}
        }


        if($person_id && $type_person){

            $column = ($type_person == 'customers') ? 'customer_id':'supplier_id';

            $data =  $data->whereHas($relation, function($query) use($column, $person_id){
                                $query->where($column, $person_id);
                            });

        }

        if($item_id){
            $data =  $data->where('item_id', $item_id);
        }

        if($web_platform_id || $brand_id || $category_id){
            $data = $data->whereHas('relation_item', function($q) use($web_platform_id, $brand_id, $category_id){
				if ($web_platform_id) {
					$q->where('web_platform_id', $web_platform_id);
                }
				if ($brand_id) {
					$q->where('brand_id', $brand_id);
				}
                if ($category_id) {
					$q->where('category_id', $category_id);
				}
            });
        }

        return $data;

    }


    private function getDataType($request){

        if($request['type'] == 'sale'){

            $data['model'] = DocumentItem::class;
            $data['relation'] = 'document';

        }else{

            $data['model'] = PurchaseItem::class;
            $data['relation'] = 'purchase';

        }

        return $data;
    }


    public function pdf(Request $request) {

        $records = $this->getRecordsItems($request->all())->latest('id')->get();
        $type_name = ($request->type == 'sale') ? 'Ventas_':'Compras_';
        $type = $request->type;
        $document_type_id = $request['document_type_id'];

        $pdf = PDF::loadView('report::general_items.report_pdf', compact("records", "type", "document_type_id"))->setPaper('a4', 'landscape');

        $filename = 'Reporte_General_Productos_'.$type_name.Carbon::now().'.xlsx';

        return $pdf->download($filename.'.pdf');
    }


    public function excel(Request $request) {

        $records = $this->getRecordsItems($request->all())->latest('id')->get();
        $type = ($request->type == 'sale') ? 'Ventas_':'Compras_';
        $document_type_id = $request['document_type_id'];

        return (new GeneralItemExport)
                ->records($records)
                ->type($request->type)
                ->document_type_id($document_type_id)
                ->download('Reporte_General_Productos_'.$type.Carbon::now().'.xlsx');

    }
}
