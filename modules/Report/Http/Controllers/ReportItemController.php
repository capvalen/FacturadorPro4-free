<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\ItemExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Document;
use App\Models\Tenant\DocumentItem;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\ItemCollection;
use Modules\Report\Traits\ReportTrait;


class ReportItemController extends Controller
{
    use ReportTrait;

    public function filter() {

        $document_types = [];
        $items = $this->getItems('items');
        $establishments = [];
        $web_platforms = $this->getWebPlatforms();

        return compact('document_types','establishments','items','web_platforms');
    }


    public function index() {

        return view('report::items.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecordsItems($request->all(), DocumentItem::class);

        return new ItemCollection($records->paginate(config('tenant.items_per_page')));
    }



    public function getRecordsItems($request, $model){

        // dd($request['period']);
        $document_type_id = $request['document_type_id'];
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $item_id = $request['item_id'];
        $web_platform_id = $request['web_platform_id'];

        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                // $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                // $d_end = $date_end;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        $records = $this->dataItems($document_type_id, $establishment_id, $d_start, $d_end, $item_id, $model, $web_platform_id);

        return $records;

    }


    private function dataItems($document_type_id, $establishment_id, $date_start, $date_end, $item_id, $model, $web_platform_id)
    {

        $data = $model::where('item_id', $item_id)
                        ->whereHas('document', function($query) use($date_start, $date_end){
                            $query
                            ->whereBetween('date_of_issue', [$date_start, $date_end])
                            ->whereIn('document_type_id', ['01','03'])
                            ->whereIn('state_type_id', ['01','03','05','07','13'])
                            ->latest()
                            ->whereTypeUser();
                        });


        if($web_platform_id){

            $data = $data->whereHas('relation_item', function($q) use($web_platform_id){
                            $q->where('web_platform_id', $web_platform_id);
                        });
                        
        }

        return $data;

    }



    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $records = $this->getRecordsItems($request->all(), DocumentItem::class)->get();

        return (new ItemExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Reporte_Ventas_por_Producto_'.Carbon::now().'.xlsx');

    }
}
