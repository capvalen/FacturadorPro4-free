<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\InventoryExport;
use Illuminate\Http\Request;
use App\Models\Tenant\{
    Establishment,
    Company,
    Item
};
use Carbon\Carbon;

class ReportInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $reports = Item::query()
            ->with(['unit_type'])
            ->where('item_type_id', '01')
            ->latest()
            ->get();

        return view('tenant.reports.inventories.index', compact('reports'));
    }

    /**
     * Search
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $reports = Item::query()
            ->with(['unit_type'])
            ->where('item_type_id', '01')
            ->latest()
            ->get();

        return view('tenant.reports.inventories.index', compact('reports'));
    }

    /**
     * PDF
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function pdf(Request $request) {
        $company = Company::first();
        $establishment = Establishment::first();

        $reports = Item::query()
            ->with(['unit_type'])
            ->where('item_type_id', '01')
            ->latest()
            ->get();

        $pdf = PDF::loadView('tenant.reports.inventories.report_pdf', compact("reports", "company", "establishment"));
        $filename = 'Reporte_Inventario'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }

    /**
     * Excel
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function excel(Request $request) {
        $company = Company::first();
        $establishment = Establishment::first();

        $records = Item::query()
            ->with(['unit_type'])
            ->where('item_type_id', '01')
            ->latest()
            ->get();

        return (new InventoryExport)
            ->records($records)
            ->company($company)
            ->establishment($establishment)
            ->download('ReporteInv'.Carbon::now().'.xlsx');
    }
}
