<?php
namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Voided;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoidedController extends Controller
{
    public function __construct()
    {
        $this->middleware('input.request:voided,api', ['only' => ['store']]);
    }

    public function store(Request $request)
    {
        $fact = DB::connection('tenant')->transaction(function () use($request) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->senderXmlSignedSummary();

            return $facturalo;
        });

        $document = $fact->getDocument();
        //$response = $fact->getResponse();

        return [
            'success' => true,
            'data' => [
                'external_id' => $document->external_id,
                'ticket' => $document->ticket,
            ]
        ];
    }

    public function status(Request $request)
    {
        if($request->has('external_id')) {
            $external_id = $request->input('external_id');
            $summary = Voided::where('external_id', $external_id)
                            ->first();
            if(!$summary) {
                throw new Exception("El código externo {$external_id} es inválido, no se encontró anulación relacionada");
            }
        } elseif ($request->has('ticket')) {
            $ticket = $request->input('ticket');
            $summary = Voided::where('ticket', $ticket)
                            ->first();
            if(!$summary) {
                throw new Exception("El ticket {$ticket} es inválido, no se encontró anulación relacionada");
            }
        } else {
            throw new Exception('Es requerido el código externo o ticket');
        }

        $facturalo = new Facturalo();
        $facturalo->setDocument($summary);
        $facturalo->setType('voided');
        $facturalo->statusSummary($summary->ticket);

        $response = $facturalo->getResponse();

        return [
            'success' => true,
            'data' => [
                'filename' => $summary->filename,
                'external_id' => $summary->external_id
            ],
            'links' => [
                'xml' => $summary->download_external_xml,
//                'pdf' => $summary->download_external_pdf,
                'cdr' => $summary->download_external_cdr,
            ],
            'response' => $response
        ];
    }
}