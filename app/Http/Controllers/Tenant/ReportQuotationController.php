<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\QuotationExport;
use Illuminate\Http\Request;
use App\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Quotation;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use App\Http\Resources\Tenant\QuotationCollection;


class ReportQuotationController extends Controller
{
    use ReportTrait;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        /*$documentTypes = DocumentType::query()
            ->where('active', 1)
            ->get();

        $establishments = Establishment::all();*/
        
        return view('tenant.reports.quotations.index') ; //, compact('documentTypes','establishments'));
    }
    
    /**
     * Search
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
       // return 'asd';
        $d = null;
        $a = null;
    
        
        if ($request->has('d') && $request->has('a')) {

            //return 'entra';
            $d = $request->d;
            $a = $request->a;
            
            $reports = Quotation::whereBetween('date_of_issue', [$d, $a])->latest();
        }
        else {
            
            $reports = Quotation::latest();
        }

        $reports = $reports->paginate(config('tenant.items_per_page'));

        //$reports = new QuotationCollection($source->paginate(config('tenant.items_per_page')));
        //return json_encode($reports);

        return view('tenant.reports.quotations.index', compact('reports', 'a', 'd'));
    }
    
    /**
     * PDF
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request) {
        $company = Company::first();
       // $establishment = Establishment::first();
       // $td = $request->td;
       // $establishment_id = $this->getEstablishmentId($request->establishment);
        
        if ($request->has('d') && $request->has('a') && ($request->d != null && $request->a != null)) {
            $d = $request->d;
            $a = $request->a;

            $reports = Quotation::whereBetween('date_of_issue', [$d, $a])->latest()->get();
            
           /* if (is_null($td)) {
                $reports = Purchase::with([ 'state_type', 'supplier'])
                    ->whereBetween('date_of_issue', [$d, p[pppppp-$a])
                    ->latest()
                    ->get();
            }
            else {
                $reports = Purchase::with([ 'state_type', 'supplier'])
                    ->whereBetween('date_of_issue', [$d, $a])
                    ->latest()
                    ->where('document_type_id', $td)
                    ->get();
            }*/
        }
        else {

            $reports = Quotation::latest()->get();
           /* if (is_null($td)) {
                $reports = Purchase::with([ 'state_type', 'supplier'])
                    ->latest()
                    ->get();
            }
            else {
                $reports = Purchase::with([ 'state_type', 'supplier'])
                    ->latest()
                    ->where('document_type_id', $td)
                    ->get();
            }*/
        }

        /*if(!is_null($establishment_id)){
            $reports = $reports->where('establishment_id', $establishment_id);
        }*/

        $pdf = PDF::loadView('tenant.reports.quotations.report_pdf', compact("reports", "company"));
        $filename = 'Reporte_Cotizacion'.date('YmdHis');
        
        return $pdf->download($filename.'.pdf');
    }
    
    /**
     * Excel
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request) {
        $company = Company::first();
       // $establishment = Establishment::first();
      //  $td= $request->td;
      //  $establishment_id = $this->getEstablishmentId($request->establishment);
       
        if ($request->has('d') && $request->has('a') && ($request->d != null && $request->a != null)) {
            $d = $request->d;
            $a = $request->a;

              $records = Quotation::whereBetween('date_of_issue', [$d, $a])->latest()->get();
            
            /*if (is_null($td)) {
                $records = Purchase::with([ 'state_type', 'supplier'])
                    ->whereBetween('date_of_issue', [$d, $a])
                    ->latest()
                    ->get();
            }
            else {
                $records = Purchase::with([ 'state_type', 'supplier'])
                    ->whereBetween('date_of_issue', [$d, $a])
                    ->latest()
                    ->where('document_type_id', $td)
                    ->get();
            }*/
        }
        else {
             $records = Quotation::latest()->get();
           /* if (is_null($td)) {
                $records = Purchase::with([ 'state_type', 'supplier'])
                    ->latest()
                    ->get();
            }
            else {
                $records = Purchase::with([ 'state_type', 'supplier'])
                    ->where('document_type_id', $td)
                    ->latest()
                    ->get();
            }*/
        }

       /*if(!is_null($establishment_id)){
            $records = $records->where('establishment_id', $establishment_id);
        }*/
        
        return (new QuotationExport)
                ->records($records)
                ->company($company)
               // ->establishment($establishment)
                ->download('ReporteCotiz'.Carbon::now().'.xlsx');
    }
}
