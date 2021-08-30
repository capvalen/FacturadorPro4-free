<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Document;
use Modules\Document\Http\Resources\ValidateDocumentsCollection;
use App\Models\Tenant\Catalogs\DocumentType;
use App\Models\Tenant\Establishment;
use App\Models\Tenant\Series;
use App\Models\Tenant\StateType;
use Modules\Document\Http\Requests\ValidateDocumentsRequest;
use App\CoreFacturalo\Services\Extras\ValidateCpe2;
use App\Models\Tenant\Company;
use Illuminate\Support\Facades\DB;

class ValidateDocumentController extends Controller
{
    
    public function index()
    {
        return view('document::validate_documents.index');
    }

    public function records(ValidateDocumentsRequest $request)
    {
        
        $records = $this->getRecords($request);
        $validate_documents = $this->validateDocuments($records);

        return new ValidateDocumentsCollection($validate_documents);

    }

    public function validateDocuments($records){

        $records_paginate = $records->paginate(config('tenant.items_per_page'));
        // $documents = $records_paginate->getCollection();

        // dd($records_paginate->getCollection());
        
        foreach ($records_paginate->getCollection() as $document)
        {
            reValidate:
            $validate_cpe = new ValidateCpe2();
            $response = $validate_cpe->search($document->company->number,
                                                $document->document_type_id,
                                                $document->series,
                                                $document->number,
                                                $document->date_of_issue,
                                                $document->total
                                            );

            if ($response['success']) {
                
                $response_code = $response['data']['comprobante_estado_codigo'];
                $response_description = $response['data']['comprobante_estado_descripcion'];

                $message = $document->number_full.'|Código: '.$response_code.'|Mensaje: '.$response_description;

                $document->message = $message; 
                $document->state_type_sunat_description = $response_description; 
                $document->code = $response_code; 
 
            } else {
                goto reValidate;
            }
        }

        return $records_paginate;
    }


    public function getRecords($request){


        $start_number = $request->start_number;
        $end_number = $request->end_number;
        $document_type_id = $request->document_type_id;
        $series = $request->series;
        
        // dd($request->all());

        if($end_number){

            $records = Document::where('document_type_id',$document_type_id)
                            ->where('series',$series)
                            ->whereBetween('number', [$start_number , $end_number])
                            ->latest();

        }else{

            $records = Document::where('document_type_id',$document_type_id)
                            ->where('series',$series)
                            ->where('number',$start_number)
                            ->latest();
        }        

        return $records;

    }

    public function data_table()
    {
        
        $document_types = DocumentType::whereIn('id', ['01', '03','07', '08'])->get();
        $series = Series::whereIn('document_type_id', ['01', '03','07', '08'])->get();
                       
        return compact('document_types','series');

    }
    
    public function regularize(ValidateDocumentsRequest $request)
    {
        
        $document_state = [
            'ACEPTADO' => '05',
            'ENVIADO' => '03',
            'OBSERVADO' => '07',
            'RECHAZADO' => '09',
            'ANULADO' => '11',
            'POR ANULAR' => '13',
        ];

        $records = $this->getRecords($request)->get();

        DB::connection('tenant')->transaction(function() use($records, $document_state){
        
            foreach ($records as $document)
            {
                reValidate:
                $validate_cpe = new ValidateCpe2();
                $response = $validate_cpe->search($document->company->number,
                                                    $document->document_type_id,
                                                    $document->series,
                                                    $document->number,
                                                    $document->date_of_issue,
                                                    $document->total
                                                );

                if ($response['success']) {

                    $response_description = mb_strtoupper($response['data']['comprobante_estado_descripcion']);

                    $state_type_id = isset($document_state[$response_description]) ? $document_state[$response_description] : null;

                    if($state_type_id){
                        $document->state_type_id = $state_type_id;
                        $document->update();
                    }
    
                } else {
                    goto reValidate;
                }
            }

        });

        return [
            'success' => true,
            'message' => 'Estados regularizados correctamente'
        ];

    }
}
