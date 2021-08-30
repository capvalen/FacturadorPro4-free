<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Document;
use Modules\Document\Http\Resources\DocumentRegularizeShippingCollection;
use App\Traits\OfflineTrait;


class DocumentRegularizeShippingController extends Controller
{

    use OfflineTrait;

    public function index()
    {

        $is_client = $this->getIsClient();

        return view('document::documents.regularize_shipping', compact('is_client'));
    }

    public function records(Request $request)
    {

        $records = $this->getRecords($request);

        return new DocumentRegularizeShippingCollection($records->paginate(config('tenant.items_per_page')));

    }

    public function getRecords($request){


        $d_end = $request->d_end;
        $d_start = $request->d_start;
        $date_of_issue = $request->date_of_issue;
        $document_type_id = $request->document_type_id;
        $number = $request->number;
        $series = $request->series;
        $state_type_id = $request->state_type_id;
        $pending_payment = ($request->pending_payment == "true") ? true:false;
        $customer_id = $request->customer_id;


        if($d_start && $d_end){

            $records = Document::where('document_type_id', 'like', '%' . $document_type_id . '%')
                            ->where('series', 'like', '%' . $series . '%')
                            ->where('number', 'like', '%' . $number . '%')
                            ->where('state_type_id', 'like', '%' . $state_type_id . '%')
                            ->whereBetween('date_of_issue', [$d_start , $d_end])
                            ->whereRegularizeShipping()
                            ->whereTypeUser()
                            ->latest();

        }else{

            $records = Document::where('date_of_issue', 'like', '%' . $date_of_issue . '%')
                            ->where('document_type_id', 'like', '%' . $document_type_id . '%')
                            ->where('state_type_id', 'like', '%' . $state_type_id . '%')
                            ->where('series', 'like', '%' . $series . '%')
                            ->where('number', 'like', '%' . $number . '%')
                            ->whereRegularizeShipping()
                            ->whereTypeUser()
                            ->latest();
        }

        if($pending_payment){
            $records = $records->where('total_canceled', false);
        }

        if($customer_id){
            $records = $records->where('customer_id', $customer_id);
        }

        return $records;

    }


    public function data_table()
    {

        return app(DocumentController::class)->data_table();

    }


}
