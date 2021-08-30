<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Barryvdh\DomPDF\Facade as PDF;
use Modules\Report\Exports\DocumentExport;
use Illuminate\Http\Request;
use Modules\Report\Traits\ReportTrait;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Document;
use App\Models\Tenant\Company;
use Carbon\Carbon;
use Modules\Report\Http\Resources\DocumentCollection;
use Modules\Item\Models\Category;


class ReportDocumentController extends Controller
{
    use ReportTrait;


    public function filter() {

        $document_types = DocumentType::whereIn('id', ['01', '03','07', '08'])->get();

        $persons = $this->getPersons('customers');
        $sellers = $this->getSellers();

        $establishments = Establishment::all()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->description
            ];
        });

        return compact('document_types','establishments','persons', 'sellers');
    }


    public function index() {
        $configuration = Configuration::first();
        $configuration->ticket_58 = (bool)$configuration->ticket_58;
        return view('report::documents.index',compact('configuration'));
    }

    public function records(Request $request)
    {
        $records = $this->getRecords($request->all(), Document::class);

        return new DocumentCollection($records->paginate(config('tenant.items_per_page')));
    }



    public function pdf(Request $request) {
        set_time_limit (1800); // Maximo 30 minutos
        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
        $records = $this->getRecords($request->all(), Document::class)->get();
        $filters = $request->all();

        $pdf = PDF::loadView('report::documents.report_pdf', compact("records", "company", "establishment", "filters"))
            ->setPaper('a4', 'landscape');

        $filename = 'Reporte_Ventas_'.date('YmdHis');

        return $pdf->download($filename.'.pdf');
    }




    public function excel(Request $request) {

        $company = Company::first();
        $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

        $records = $this->getRecords($request->all(), Document::class)->get();
        $filters = $request->all();

        //get categories
        $categories = [];
        $categories_services = [];

        if($request->include_categories == "true"){
            $categories = $this->getCategories($records, false);
            $categories_services = $this->getCategories($records, true);
        }


        return (new DocumentExport)
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->filters($filters)
                ->categories($categories)
                ->categories_services($categories_services)
                ->download('Reporte_Ventas_'.Carbon::now().'.xlsx');

    }


    public function getCategories($records, $is_service) {

        $aux_categories = collect([]);

        foreach ($records as $document) {

            $id_categories = $document->items->filter(function($row) use($is_service){
                return (($is_service) ? (!is_null($row->relation_item->category_id) && $row->item->unit_type_id === 'ZZ') : !is_null($row->relation_item->category_id)) ;
            })->pluck('relation_item.category_id');

            foreach ($id_categories as $value) {
                $aux_categories->push($value);
            }
        }

        return Category::whereIn('id', $aux_categories->unique()->toArray())->get();

    }


}
