<?php

namespace Modules\Report\Traits;

use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Document;
use App\Models\Tenant\SaleNote;
use Carbon\Carbon;
use App\Models\Tenant\Person;
use App\Models\Tenant\Item;
use App\Models\Tenant\Series;
use App\Models\Tenant\User;
use App\Models\Tenant\StateType;
use Auth;
use Illuminate\Support\Facades\DB;
use Modules\Item\Models\Brand;
use Modules\Item\Models\Category;
use Modules\Item\Models\WebPlatform;


trait ReportTrait
{


    public function getRecords($request, $model)
    {
        $document_type_id = $request['document_type_id'];
        $establishment_id = $request['establishment_id'];
        $period = $request['period'];
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $month_start = $request['month_start'];
        $month_end = $request['month_end'];
        $person_id = $request['person_id'];
        $type_person = $request['type_person'];
        $seller_id = $request['seller_id'];
        $state_type_id = $request['state_type_id'];
        $purchase_order = $request['purchase_order'] ?? null;
        $guides = $request['guides'] ?? null;


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

        $records = $this->data($document_type_id, $establishment_id, $d_start, $d_end, $person_id, $type_person, $model, $seller_id, $state_type_id, $purchase_order,$guides);

        return $records;

    }


    private function data($document_type_id, $establishment_id, $date_start, $date_end, $person_id, $type_person, $model, $seller_id, $state_type_id, $purchase_order, $guides = null)
    {

        if($document_type_id && $establishment_id){

            $data = $model::where([['establishment_id', $establishment_id],['document_type_id', $document_type_id]])
                                ->whereBetween('date_of_issue', [$date_start, $date_end])->latest()->whereTypeUser();

        }elseif($document_type_id){

            $data = $model::whereBetween('date_of_issue', [$date_start, $date_end])->latest()
                                ->where('document_type_id', 'like', '%' . $document_type_id . '%')->whereTypeUser();

        }elseif($establishment_id){

            $data = $model::whereBetween('date_of_issue', [$date_start, $date_end])->latest()
                                ->where('establishment_id', 'like', '%' . $establishment_id . '%')->whereTypeUser();

        }else{
            $data = $model::whereBetween('date_of_issue', [$date_start, $date_end])->latest()->whereTypeUser();
        }

        if($person_id && $type_person){

            $column = ($type_person == 'customers') ? 'customer_id':'supplier_id';
            $data =  $data->where($column, $person_id);

        }

        if($seller_id)
        {
            $data =  $data->where('user_id', $seller_id);
        }

        if($state_type_id){
            $data =  $data->where('state_type_id', $state_type_id);
        }
        if($purchase_order){
            $data =  $data->where('purchase_order', $purchase_order);
        }
        if($model == 'App\Models\Tenant\Document'){
            if(!empty($guides)){
                $data->where('guides','like', DB::raw("%\"number\":\"%").$guides. DB::raw("%\"%"));
            }
        }

        return $data;

    }



    public function getRecordsCash($request){

        $document_type_id = $request['document_type_id'];
        $user_id = $request['user_id'];

        $records = $this->dataCash($document_type_id, $user_id);

        return $records;

    }


    private function dataCash($document_type_id, $user_id)
    {

        $sale_notes = [];

        switch ($document_type_id) {
            case '01':
                $documents = Document::whereIn('document_type_id',['01','03'])->latest();
                $sale_notes = SaleNote::latest();
                break;

            case '02':
                $documents = Document::whereIn('document_type_id',['01','03'])->latest();
                break;

            case '03':
                $sale_notes = SaleNote::latest();
                break;
        }

        foreach ($sale_notes as $sn) {
            $documents->push($sn);
        }

        $data = $documents;

        return $data;

    }



    public function getPersons($type){

        $persons = Person::whereType($type)->orderBy('name')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->number.' - '.$row->name,
                'name' => $row->name,
                'number' => $row->number,
                'identity_document_type_id' => $row->identity_document_type_id,
            ];
        });

        return $persons;

    }


    public function getDataTablePerson($type, $request) {

        $persons = Person::where('number','like', "%{$request->input}%")
                            ->orWhere('name','like', "%{$request->input}%")
                            ->whereType($type)->orderBy('name')
                            ->get()->transform(function($row) {
                                return [
                                    'id' => $row->id,
                                    'description' => $row->number.' - '.$row->name,
                                    'name' => $row->name,
                                    'number' => $row->number,
                                    'identity_document_type_id' => $row->identity_document_type_id,
                                ];
                            });

        return $persons;

    }

    public function getItems(){

        $items = Item::orderBy('description')->take(20)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => ($row->internal_id) ? "{$row->internal_id} - {$row->description}" :$row->description,
            ];
        });

        return $items;

    }


    public function getDataTableItem($request) {

        $items = Item::where('description','like', "%{$request->input}%")
                        ->orWhere('internal_id','like', "%{$request->input}%")
                        ->orderBy('description')
                        ->get()->transform(function($row) {
                            return [
                                'id' => $row->id,
                                'description' => ($row->internal_id) ? "{$row->internal_id} - {$row->description}" :$row->description,
                            ];
                        });

        return $items;

    }

    public function getSellers(){

        $persons = User::whereIn('type', ['seller', 'admin'])->orderBy('name')->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'type' => $row->type,
            ];
        });

        return $persons;

    }

    public function getSeries($document_types)
    {
        $series = Series::wherein('document_type_id', $document_types->pluck('id')->toArray());
        return $series->get();
    }

    public function getEstablishment()
    {
        $establishment = Establishment::select('id', 'description');
        if (Auth::user()->type !== 'admin') {
            $establishment = $establishment->where('id', Auth::user()->establishment_id);
        }

        return $establishment->get();
    }

    public function getDataOfPeriod($request){

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

        return [
            'd_start' => $d_start,
            'd_end' => $d_end
        ];
    }

    public function getDateRangeTypes($is_sale = false){

        if($is_sale){

            return [
                ['id' => 'date_of_issue', 'description' => 'Fecha emisión'],
            ];

        }

        return [
            ['id' => 'date_of_issue', 'description' => 'Fecha emisión'],
            ['id' => 'delivery_date', 'description' => 'Fecha entrega']
        ];

    }

    public function getOrderStateTypes(){

        return [
            ['id' => 'all_states', 'description' => 'Todos'],
            ['id' => 'pending', 'description' => 'Pendiente'],
            ['id' => 'processed', 'description' => 'Procesado'],
        ];

    }

    public function getCIDocumentTypes(){

        return DocumentType::whereIn('id', ['01', '03', '80'])->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'description' => $row->description
            ];
        });

    }

    public function getStateTypesById($params){

        return StateType::whereIn('id', $params)->get()->transform(function($row) {
            return [
                'id' => $row->id,
                'name' => $row->description
            ];
        });

    }

    public function getWebPlatforms(){

        return WebPlatform::get();

    }

    public function getBrands()
    {
        return Brand::orderBy('name')
            ->get();
    }

    public function getCategories()
    {
        return Category::orderBy('name')
            ->get();
    }

    public function getUsers()
    {
        $user = auth()->user();
        $persons = User::select('id', 'name', 'type')
                ->orderBy('name');
        if ($user->type === 'admin') {
            $persons = $persons->whereIn('type', ['seller', 'admin'])
                ->get();
        } else {
            $persons = $persons->where('id', $user->id)
                ->get();
        }
        return $persons;
    }
}
