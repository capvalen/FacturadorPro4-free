<?php
namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Services\Dni\Dni;
use App\CoreFacturalo\Services\Extras\ExchangeRate;
use App\CoreFacturalo\Services\Ruc\Sunat;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use Illuminate\Http\Request;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\WS\Services\ConsultCdrService;
use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\WS\Validator\XmlErrorCodeProvider;
use App\CoreFacturalo\WS\Client\WsClient;
use App\CoreFacturalo\WS\Services\SunatEndpoints;
use App\Http\Requests\Tenant\ServiceRequest;
use Exception;
use Modules\Document\Helpers\ConsultCdr;


class ServiceController extends Controller
{


    protected $wsClient;
    protected $document;
    use StorageDocument;
    const ACCEPTED = '05';

    public function consultCdrStatus(ServiceRequest $request){

        $document_type_id = $request->codigo_tipo_documento;
        $series = $request->serie_documento;
        $number = $request->numero_documento;

        $this->document = Document::where([['soap_type_id','02'],
                                            ['document_type_id',$document_type_id],
                                            ['series',$series],
                                            ['number',$number]
                                            ])->first();

        // if(!$this->document)  throw new Exception("Documento no encontrado");
        if(!$this->document)  return [
            'success' => false,
            'message' => "Documento no encontrado"
        ];

        return (new ConsultCdr)->search($this->document);

    }


    public function ruc($number)
    {
        $service = new Sunat();
        $res = $service->get($number);
        if ($res) {
            $province_id = Province::idByDescription($res->provincia);
            return [
                'success' => true,
                'data' => [
                    'name' => $res->razonSocial,
                    'trade_name' => $res->nombreComercial,
                    'address' => $res->direccion,
                    'phone' => implode(' / ', $res->telefonos),
                    'department' => ($res->departamento)?:'LIMA',
                    'department_id' => Department::idByDescription($res->departamento),
                    'province' => ($res->provincia)?:'LIMA',
                    'province_id' => $province_id,
                    'district' => ($res->distrito)?:'LIMA',
                    'district_id' => District::idByDescription($res->distrito, $province_id),
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $service->getError()
            ];
        }
    }

    public function dni($number)
    {
        $res = Dni::search($number);

        return $res;
    }

    public function exchangeRateTest($date)
    {
        $sale = 1;
        if($date <= now()->format('Y-m-d')) {
            $ex_rate = \App\Models\Tenant\ExchangeRate::where('date', $date)->first();
            if ($ex_rate) {
                $sale = $ex_rate->sale;
            } else {
                $exchange_rate = new ExchangeRate();
                $res = $exchange_rate->searchDate($date);
                if ($res) {
                    $ex_rate = \App\Models\Tenant\ExchangeRate::create([
                        'date' => $date,
                        'date_original' => $res['date_data'],
                        'purchase' => $res['data']['purchase'],
                        'purchase_original' => $res['data']['purchase'],
                        'sale' => $res['data']['sale'],
                        'sale_original' => $res['data']['sale']
                    ]);
                    $sale = $ex_rate->sale;
                }else{
                    $last_ex_rate = \App\Models\Tenant\ExchangeRate::orderBy('date', 'desc')->first();
                    if ($last_ex_rate) {
                        $sale = $last_ex_rate->sale;
                    }
                    else {
                        $sale = 0;
                    }
                }
            }
        }
        return [
            'date' => $date,
            'sale' => $sale
        ];
    }

    public function documentStatus(Request $request) {
        if($request->has('external_id') OR $request->has('serie_number')) {
            $external_id = $request->input('external_id');
            $request_serie = $request->input('serie_number');
            $serie_number = explode('-', $request_serie);
            $serie = $serie_number[0];
            $number = $serie_number[1];

            if(!$external_id) {
                $document = Document::where('number', $number)
                            ->where('series', $serie)
                            ->first();
            } else {
                $document = Document::where('external_id', $external_id)
                            ->where('number', $number)
                            ->where('series', $serie)
                            ->first();
            }

            if(!$document) {
                throw new Exception("El documento con código externo {$external_id} o numero {$request_serie}, no se encuentra registrado.");
            }
            return [
                'success' => true,
                'data' => [
                    'number' => $document->number_full,
                    'filename' => $document->filename,
                    'external_id' => $document->external_id,
                    'status_id' => $document->state_type_id,
                    'status' => $document->state_type->description,
                    'qr' => $document->qr,
                    'number_to_letter' => $document->number_to_letter,
                ],
                'links' => [
                    'xml' => $document->download_external_xml,
                    'pdf' => $document->download_external_pdf,
                    'cdr' => ($document->download_external_cdr)?$document->download_external_cdr:'',
                ],
            ];
        }
    }

}
