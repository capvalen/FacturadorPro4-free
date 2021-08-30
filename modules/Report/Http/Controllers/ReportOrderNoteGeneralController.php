<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\OrderNoteGeneralCollection;
use Modules\Report\Traits\ReportTrait;
use Modules\Order\Models\OrderNote;


class ReportOrderNoteGeneralController extends Controller
{
    use ReportTrait;

    public function filter() {


        $persons = $this->getPersons('customers'); 
        $date_range_types = $this->getDateRangeTypes();
        $order_state_types = $this->getOrderStateTypes();
        $sellers = $this->getSellers();

        return compact('persons', 'date_range_types', 'order_state_types', 'sellers');
    }


    public function index() {

        return view('report::order_notes_general.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecordsOrderNotes($request->all(), OrderNote::class);

        return new OrderNoteGeneralCollection($records->paginate(config('tenant.items_per_page')));
    }



    public function getRecordsOrderNotes($request, $model){

        // dd($request);

        $records = $this->dataOrderNotes($request, $model);

        return $records;

    }


    private function dataOrderNotes($request, $model)
    {

        $order_state_type_id = $request['order_state_type_id'];

        switch ($order_state_type_id) {

            case 'pending':
                $data = $model::wherePendingState($request);
                break;

            case 'processed':
                $data = $model::whereProcessedState($request);
                break;

            default: 
                $data = $model::whereDefaultState($request);
                break;
        }

        return $data->whereTypeUser()->latest();

    }


    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecordsOrderNotes($request->all(), OrderNote::class)->get();
        $params = $request->all();

        $pdf = PDF::loadView('report::order_notes_general.report_pdf', compact("records", "company", "establishment", "params"));

        $filename = 'Reporte_General_Pedidos_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }

 

}
