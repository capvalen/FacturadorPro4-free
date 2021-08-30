<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\FixedAssetPurchaseExport;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\FixedAssetPurchaseCollection;
use Modules\Purchase\Models\FixedAssetPurchase;

class ReportFixedAssetPurchaseController extends Controller
{

    use ReportTrait;

    public function filter() {

        $document_types = DocumentType::whereIn('id', ['01', '03','GU75', 'NE76'])->get();

        $persons = $this->getPersons('suppliers');
        $sellers = $this->getSellers();

        $establishments = Establishment::all()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->description
            ];
        });

        return compact('document_types','establishments', 'persons', 'sellers');
    }


    public function index() {

        return view('report::fixed-asset-purchases.index');
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request->all(), FixedAssetPurchase::class);

        return new FixedAssetPurchaseCollection($records->paginate(config('tenant.items_per_page')));
    }



    public function pdf(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all(), FixedAssetPurchase::class)->get();
        $filters = $request->all();

        $pdf = PDF::loadView('report::fixed-asset-purchases.report_pdf', compact("records", "company", "establishment", "filters"));

        $filename = 'Reporte_A_Fijos_Compras_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }




    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all(), FixedAssetPurchase::class)->get();
        $filters = $request->all();

        return (new FixedAssetPurchaseExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->filters($filters)
                ->download('Reporte_A_Fijos_Compras_'.Carbon::now().'.xlsx');

    }
}
