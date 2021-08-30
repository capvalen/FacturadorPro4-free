<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\DocumentHotelExport;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\BusinessTurn\Models\DocumentHotel;
use Modules\BusinessTurn\Models\BusinessTurn;
use Modules\Report\Http\Resources\DocumentHotelCollection;

class ReportDocumentHotelController extends Controller
{
    // use ReportTrait;
   
     
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
      

    public function index() 
    {
        $record = BusinessTurn::where('value','hotel')->where('active',true)->first();

        if(!$record){
            return redirect()->route('tenant.reports.sale_notes.index');
        }

        return view('report::document_hotels.index');
    }
   
    public function records(Request $request)
    {
        $records = $this->getRecords($request->all());

        return new DocumentHotelCollection($records->paginate(config('tenant.items_per_page')));
    }
 

    public function excel(Request $request) {
    
        $company = Company::first();
        
        $records = $this->getRecords($request->all())->get();

        return (new DocumentHotelExport)
                ->records($records)
                ->company($company)
                ->download('Reporte_Hoteles_'.Carbon::now().'.xlsx');

    }


    public function getRecords($request){
 
        $date_start = $request['date_start'];
        $date_end = $request['date_end']; 
 
        $records = $this->data( $date_start, $date_end);

        return $records;

    }


    private function data($date_start, $date_end)
    {

        if($date_start && $date_end){

            $data = DocumentHotel::where([['date_entry','>=', $date_start],['date_exit','<=', $date_end]])->latest();

        }else{
            $data = DocumentHotel::latest();
        }
       
        return $data;
        
    }

}
