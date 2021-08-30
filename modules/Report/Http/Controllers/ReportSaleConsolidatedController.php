<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Company;
use App\Models\Tenant\Item;
use Carbon\Carbon;
use Modules\Report\Http\Resources\SaleConsolidatedCollection;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\SaleNoteItem;
use App\Models\Tenant\DocumentItem;
use Illuminate\Support\Facades\DB;
use Modules\Report\Exports\SaleConsolidatedExport;
use Modules\Report\Exports\SaleConsolidatedTotalExport;


class ReportSaleConsolidatedController extends Controller
{
    use ReportTrait;

    public function filter()
    {


        $persons = $this->getPersons('customers');
        $date_range_types = $this->getDateRangeTypes(true);
        $order_state_types = [];
        $sellers = $this->getSellers();
        $document_types = $this->getCIDocumentTypes();
        $establishment_id = $this->getEstablishment();
        $series = $this->getSeries($document_types);

        return compact('persons', 'date_range_types', 'order_state_types', 'sellers', 'document_types', 'series', 'establishment_id');
    }


    public function index() {

        return view('report::sales_consolidated.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecordsSalesConsolidated($request->all());

        return new SaleConsolidatedCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function totalsByItem(Request $request)
    {

        $records = $this->getRecordsSalesConsolidated($request->all())->get()->groupBy('item_id');

        return $records->map(function($row, $key){

            return [
                'item_id' => $key,
                'item_internal_id' => $row->first()->relation_item->internal_id,
                'item_unit_type_id' => $row->first()->relation_item->unit_type_id,
                'item_description' => $row->first()->item->description,
                'quantity' => number_format($row->sum('quantity'), 4, ".", ""),
            ];
        });

    }


    public function getRecordsSalesConsolidated($request){

        // dd($request);
        $records = $this->dataSalesConsolidated($request);

        return $records;

    }


    private function dataSalesConsolidated($request)
    {

        $document_types = isset($request['document_type_id']) ? json_decode($request['document_type_id']) : [];
        $document_items = DocumentItem::whereDefaultDocumentType($request);
        if (!empty($document_types)) {
            $nota_venta = null;
            $document_items = $document_items->wherein('document_type_id', $document_types);
            if (in_array('80', $document_types)) {
                $request['document_type_id'] = '80';
                $nota_venta = SaleNoteItem::whereDefaultDocumentType($request);
            }
            $data = ($nota_venta != null) ? $document_items->union($nota_venta) : $document_items;
        } else {
            $sale_note_items = SaleNoteItem::whereDefaultDocumentType($request);
            $data = $document_items->union($sale_note_items);
        }

        return $data;

        $document_type_id = $request['document_type_id'];

        switch ($document_type_id) {

            case '01':
            case '03':
                $data = DocumentItem::whereDefaultDocumentType($request)->whereDocumentTypeId($document_type_id);
                break;

            case '80':
                $data = SaleNoteItem::whereDefaultDocumentType($request);
                break;

            default:
                $document_items = DocumentItem::whereDefaultDocumentType($request);
                $sale_note_items = SaleNoteItem::whereDefaultDocumentType($request);
                $data = $document_items->union($sale_note_items);

                break;
        }

        return $data;

    }


    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecordsSalesConsolidated($request->all())->get();
        $params = $request->all();

        $pdf = PDF::loadView('report::sales_consolidated.report_pdf', compact("records", "company", "establishment", "params"));

        $filename = 'Reporte_Consolidado_Items_Ventas_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }


    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $records = $this->getRecordsSalesConsolidated($request->all())->get();
        $params = $request->all();
        $filename = 'Reporte_Consolidado_Items_Ventas_'.date('YmdHis');

        return (new SaleConsolidatedExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->params($params)
                ->download($filename.'.xlsx');

    }


    public function pdfTotals(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->totalsByItem($request)->sortBy('item_id');
        $params = $request->all();

        $pdf = PDF::loadView('report::sales_consolidated.report_pdf_totals', compact("records", "company", "establishment", "params"));

        $filename = 'Reporte_Consolidado_Items_Ventas_Totales_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }


    public function excelTotals(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->totalsByItem($request)->sortBy('item_id');
        $params = $request->all();
        $filename = 'Reporte_Consolidado_Items_Ventas_Totales_'.date('YmdHis');

        return (new SaleConsolidatedTotalExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->params($params)
                ->download($filename.'.xlsx');

    }

}
