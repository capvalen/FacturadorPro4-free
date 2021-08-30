<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\CommissionExport;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\SaleNote;
use App\Models\Tenant\User;
use App\Models\Tenant\Document;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\ReportCommissionCollection;

class ReportCommissionController extends Controller
{


    public function filter() {

        $document_types = [];

        $establishments = Establishment::all()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->description
            ];
        });

        return compact('document_types','establishments');
    }


    public function index() {

        return view('report::commissions.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request->all(), User::class);
        
        return new ReportCommissionCollection($records->paginate(config('tenant.items_per_page')));
    }






    
    public function getRecords($request, $model){

        $document_type_id = $request['document_type_id'];
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];

        $d_start = null;
        $d_end = null;

        switch ($period) {
            case 'month':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_start.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'between_months':
                $d_start = Carbon::parse($month_start.'-01')->format('Y-m-d');
                $d_end = Carbon::parse($month_end.'-01')->endOfMonth()->format('Y-m-d');
                break;
            case 'date':
                $d_start = $date_start;
                $d_end = $date_start;
                break;
            case 'between_dates':
                $d_start = $date_start;
                $d_end = $date_end;
                break;
        }

        $records = $this->data($document_type_id, $establishment_id, $d_start, $d_end, $model);

        return $records;

    }


    private function data($document_type_id, $establishment_id, $date_start, $date_end, $model)
    {
 
        if($establishment_id){

            $data = $model::with(['documents'=>function($q) use($date_start, $date_end){

                                $q->whereIn('state_type_id', ['01','03','05','07','13'])
                                ->whereIn('document_type_id', ['01','03','08'])
                                ->whereBetween('date_of_issue', [$date_start, $date_end]);

                            },'sale_notes'=>function($z) use($date_start, $date_end){

                                $z->whereIn('state_type_id', ['01','03','05','07','13'])
                                ->whereBetween('date_of_issue', [$date_start, $date_end]);

                            }])
                            ->where('establishment_id', $establishment_id)
                            ->latest()
                            ->whereTypeUser();

        }else{

            $data = $model::with(['documents'=>function($q) use($date_start, $date_end){
                            
                                $q->whereIn('state_type_id', ['01','03','05','07','13'])
                                ->whereIn('document_type_id', ['01','03','08'])
                                ->whereBetween('date_of_issue', [$date_start, $date_end]);
                            
                            },'sale_notes'=>function($z) use($date_start, $date_end){

                                $z->whereIn('state_type_id', ['01','03','05','07','13'])
                                ->whereBetween('date_of_issue', [$date_start, $date_end]);
                            
                            }])
                            ->latest()
                            ->whereTypeUser();
        }

        return $data;

    }

 


    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all(), User::class)->get();

        $pdf = PDF::loadView('report::commissions.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Reporte_Comision_Vendedor_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }




    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $records = $this->getRecords($request->all(), User::class)->get();

        return (new CommissionExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Reporte_Comision_Vendedor'.Carbon::now().'.xlsx');

    }
}
