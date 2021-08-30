<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\PurchaseExport;
use Illuminate\Http\Request;
use App\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Company;
use Carbon\Carbon;

class ReportPurchaseController extends Controller
{
    use ReportTrait;
    
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index() {
    //     $documentTypes = DocumentType::query()
    //         ->where('active', 1)
    //         ->get();

    //     $establishments = Establishment::all();
        
    //     return view('tenant.reports.purchases.index', compact('documentTypes','establishments'));
    // }
    
    // /**
    //  * Search
    //  * @param  Request $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function search(Request $request) {
    //    // return 'asd';
    //     $documentTypes = DocumentType::all();
    //     $td = $this->getTypeDoc($request->document_type);
    //     $d = null;
    //     $a = null;
    //     $establishments = Establishment::all();
    //     $establishment = $request->establishment;
    //     $establishment_id = $this->getEstablishmentId($establishment);
        
    //     if ($request->has('d') && $request->has('a') && ($request->d != null && $request->a != null)) {
    //         $d = $request->d;
    //         $a = $request->a;
            
    //         if (is_null($td)) {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest();
    //         }
    //         else {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest()
    //                 ->where('document_type_id', $td);
    //         }
    //     }
    //     else {
    //         if (is_null($td)) {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->latest();
    //         } else {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->latest()
    //                 ->where('document_type_id', $td);
    //         }
    //     }

    //     if(!is_null($establishment_id)){
    //         $reports = $reports->where('establishment_id', $establishment_id);
    //     }

    //     $reports = $reports->paginate(config('tenant.items_per_page'));

    //     return view('tenant.reports.purchases.index', compact('reports', 'a', 'd', 'td', 'documentTypes',"establishment","establishments"));
    // }
    
    // /**
    //  * PDF
    //  * @param  Request $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function pdf(Request $request) {
    //     $company = Company::first();
    //     $establishment = Establishment::first();
    //     $td = $request->td;
    //     $establishment_id = $this->getEstablishmentId($request->establishment);
        
    //     if ($request->has('d') && $request->has('a') && ($request->d != null && $request->a != null)) {
    //         $d = $request->d;
    //         $a = $request->a;
            
    //         if (is_null($td)) {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest()
    //                 ->get();
    //         }
    //         else {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest()
    //                 ->where('document_type_id', $td)
    //                 ->get();
    //         }
    //     }
    //     else {
    //         if (is_null($td)) {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->latest()
    //                 ->get();
    //         }
    //         else {
    //             $reports = Purchase::with([ 'state_type', 'supplier'])
    //                 ->latest()
    //                 ->where('document_type_id', $td)
    //                 ->get();
    //         }
    //     }

    //     if(!is_null($establishment_id)){
    //         $reports = $reports->where('establishment_id', $establishment_id);
    //     }

    //     $pdf = PDF::loadView('tenant.reports.purchases.report_pdf', compact("reports", "company", "establishment"));
    //     $filename = 'Reporte_Compras'.date('YmdHis');
        
    //     return $pdf->download($filename.'.pdf');
    // }
    
    // /**
    //  * Excel
    //  * @param  Request $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function excel(Request $request) {
    //     $company = Company::first();
    //     $establishment = Establishment::first();
    //     $td= $request->td;
    //     $establishment_id = $this->getEstablishmentId($request->establishment);
       
    //     if ($request->has('d') && $request->has('a') && ($request->d != null && $request->a != null)) {
    //         $d = $request->d;
    //         $a = $request->a;
            
    //         if (is_null($td)) {
    //             $records = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest()
    //                 ->get();
    //         }
    //         else {
    //             $records = Purchase::with([ 'state_type', 'supplier'])
    //                 ->whereBetween('date_of_issue', [$d, $a])
    //                 ->latest()
    //                 ->where('document_type_id', $td)
    //                 ->get();
    //         }
    //     }
    //     else {
    //         if (is_null($td)) {
    //             $records = Purchase::with([ 'state_type', 'supplier'])
    //                 ->latest()
    //                 ->get();
    //         }
    //         else {
    //             $records = Purchase::with([ 'state_type', 'supplier'])
    //                 ->where('document_type_id', $td)
    //                 ->latest()
    //                 ->get();
    //         }
    //     }

    //     if(!is_null($establishment_id)){
    //         $records = $records->where('establishment_id', $establishment_id);
    //     }
        
    //     return (new PurchaseExport)
    //             ->records($records)
    //             ->company($company)
    //             ->establishment($establishment)
    //             ->download('ReporteCom'.Carbon::now().'.xlsx');
    // }
}
