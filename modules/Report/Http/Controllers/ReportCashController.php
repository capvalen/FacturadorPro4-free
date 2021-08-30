<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\DocumentExport;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\User;
use App\Models\Tenant\Document;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\CashCollection;
 

class ReportCashController extends Controller
{
    use ReportTrait;
   
     
    public function filter() {

        $document_types = [
            ['id' => '01', 'description' => 'TODOS LOS COMPROBANTES'],
            ['id' => '02', 'description' => 'FACTURA - BOLETA DE VENTA ELECTRÓNICA'],
            ['id' => '03', 'description' => 'NOTAS DE VENTA'],
        ];

        $users = User::get(['id','name']);
        
        return compact('document_types','users');
    }
      

    public function index() {
       
        return view('report::cash.index');
    }
   
    public function records(Request $request)
    {
        $records = $this->getRecordsCash($request->all());
// dd($records);
        return new CashCollection($records->paginate(config('tenant.items_per_page')));
    }

 

    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment; 
        $records = $this->getRecords($request->all(), Document::class)->get();
        
        $pdf = PDF::loadView('report::documents.report_pdf', compact("records", "company", "establishment"));

        $filename = 'Reporte_Ventas_'.date('YmdHis');
        
        return $pdf->download($filename.'.pdf');
    }
    
  
    

    public function excel(Request $request) {
    
        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment; 
        
        $records = $this->getRecords($request->all(), Document::class)->get();

        return (new DocumentExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Reporte_Ventas_'.Carbon::now().'.xlsx');

    }
}
