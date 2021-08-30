<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Inventory\Exports\KardexLotsExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Kardex;
use App\Models\Tenant\Item;
use Carbon\Carbon;
use Modules\Inventory\Models\InventoryKardex;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Http\Resources\ReportKardexCollection;
use Modules\Inventory\Http\Resources\ReportKardexLotsCollection;
use Modules\Inventory\Models\ItemWarehouse;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Item\Models\ItemLot;
use Modules\Inventory\Http\Resources\ReportKardexLotsGroupCollection;
use Modules\Inventory\Http\Resources\ReportKardexItemLotCollection;
use Modules\Inventory\Helpers\InventoryKardexLots;
 

class ReportKardexLotsController extends Controller
{ 
 
    public function getRecords($request){

        $item_id = $request['item_id'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];

        $records = $this->data($item_id, $date_start, $date_end);

        return $records;

    }


    private function data($item_id, $date_start, $date_end)
    {

        if($date_start && $date_end){

            $data = ItemLotsGroup::whereBetween('date_of_due', [$date_start, $date_end])
                        ->orderBy('item_id')->orderBy('id');

        }else{

            $data = ItemLotsGroup::orderBy('item_id')->orderBy('id');
        }

        if($item_id){
            $data = $data->where('item_id', $item_id);
        }

        return $data;

    }

 
    public function pdf(Request $request) {

        $records = InventoryKardexLots::transformRecords($this->getRecords($request->all())->get());
        $company = Company::first();
        $establishment = Establishment::first();

        $pdf = PDF::loadView('inventory::reports.kardex_lots.report_pdf', compact("records", "company", "establishment"));
        $filename = 'Reporte_Kardex_lotes'.date('YmdHis');

        return $pdf->download($filename.'.pdf');

    }

    
    public function excel(Request $request) {

        $company = Company::first();
        $establishment = Establishment::first();
        $records = InventoryKardexLots::transformRecords($this->getRecords($request->all())->get());

        return (new KardexLotsExport)
            ->records($records)
            ->company($company)
            ->establishment($establishment)
            ->download('ReporteKardexLotes'.Carbon::now().'.xlsx');

    }

 
}
