<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Exports\ValuedKardexExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Item;
use Carbon\Carbon;
use Modules\Inventory\Http\Resources\ReportValuedKardexCollection;
use Modules\Report\Traits\ReportTrait;
use Modules\Inventory\Helpers\InventoryValuedKardex;

class ReportValuedKardexController extends Controller
{
    
    use ReportTrait;

    public function filter() {

        $establishments = Establishment::all()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->description
            ];
        });

        return compact('establishments');
    }


    public function index() {

        return view('inventory::reports.valued_kardex.index');
    }


    public function records(Request $request)
    {
        $records = $this->getRecords($request->all());

        return new ReportValuedKardexCollection($records->paginate(config('tenant.items_per_page')));
    }
 
    
    public function getRecords($request){

        $data_of_period = $this->getDataOfPeriod($request);

        $params = (object)[
            'establishment_id' => $request['establishment_id'],
            'date_start' => $data_of_period['d_start'],
            'date_end' => $data_of_period['d_end'],
        ];

        $records = $this->data($params);

        return $records;

    }


    private function data($params)
    {

        $data = Item::whereFilterValuedKardex($params)
                    ->whereNotService()
                    ->orderBy('description');

        // dd($data->get()[0]->document_items);

        return $data;

    }


    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $records = InventoryValuedKardex::getTransformRecords($this->getRecords($request->all())->get());
        // dd($records);
        // return $records;

        return (new ValuedKardexExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Reporte_Kardex_Valorizado_'.Carbon::now().'.xlsx');

    }
}
