<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\KardexExport;
use Illuminate\Http\Request;
use App\Models\Tenant\{
    Establishment,
    Company,
    Kardex,
    Item
};
use Carbon\Carbon;

class ReportKardexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $items = Item::query()
            ->where('item_type_id', '01')
            ->latest()
            ->get();
            
        return view('tenant.reports.kardex.index', compact('items'));
    }
    
    /**
     * Search
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $balance = 0;
        
        $items = Item::query()
            ->where('item_type_id', '01')
            ->latest()
            ->get();
        
        $reports = Kardex::query()
            ->with(['document', 'purchase', 'item' => function($queryItem) {
                return $queryItem->where('item_type_id', '01');
            }])
            ->where('item_id', $request->item_id)
            ->orderBy('id')
            ->get();
            
        
        return view('tenant.reports.kardex.index', compact('items', 'reports', 'balance'));
    }
    
    /**
     * PDF
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request) {
        $balance = 0;
        $company = Company::first();
        $establishment = Establishment::first();
        
        $reports = Kardex::query()
            ->with(['document', 'purchase', 'item' => function($queryItem) {
                return $queryItem->where('item_type_id', '01');
            }])
            ->where('item_id', $request->item_id)
            ->orderBy('id')
            ->get();
        
        $pdf = PDF::loadView('tenant.reports.kardex.report_pdf', compact("reports", "company", "establishment", "balance"));
        $filename = 'Reporte_Kardex'.date('YmdHis');
        
        return $pdf->download($filename.'.pdf');
    }
    
    /**
     * Excel
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request) {
        $balance = 0;
        $company = Company::first();
        $establishment = Establishment::first();
       
        $records = Kardex::query()
            ->with(['document', 'purchase', 'item' => function($queryItem) {
                return $queryItem->where('item_type_id', '01');
            }])
            ->where('item_id', $request->item_id)
            ->orderBy('id')
            ->get();
        
        return (new KardexExport)
            ->balance($balance)
            ->records($records)
            ->company($company)
            ->establishment($establishment)
            ->download('ReporteKar'.Carbon::now().'.xlsx');
    }
}
