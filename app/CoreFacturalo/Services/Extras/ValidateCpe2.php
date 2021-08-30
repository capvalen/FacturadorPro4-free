<?php

namespace App\CoreFacturalo\Services\Extras;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ValidateCpe2
{
    const URL_CONSULT = 'consultaIndividual';
    const URL_CAPTCHA = 'doCaptcha?accion=image';

    protected $client;

    protected $document_state = [
        '-' => '-',
        '0' => 'NO EXISTE',
        '1' => 'ACEPTADO',
        '2' => 'ANULADO',
        '3' => 'AUTORIZADO',
        '4' => 'NO AUTORIZADO'
    ];

    protected $company_state = [
        '-' => '-',
        '00' => 'ACTIVO',
        '01' => 'BAJA PROVISIONAL',
        '02' => 'BAJA PROV. POR OFICIO',
        '03' => 'SUSPENSION TEMPORAL',
        '10' => 'BAJA DEFINITIVA',
        '11' => 'BAJA DE OFICIO',
        '12' => 'BAJA MULT.INSCR. Y OTROS ',
        '20' => 'NUM. INTERNO IDENTIF.',
        '21' => 'OTROS OBLIGADOS',
        '22' => 'INHABILITADO-VENT.UNICA',
        '30' => 'ANULACION - ERROR SUNAT   '
    ];

    protected $company_condition = [
        '-' => '-',
        '00' => 'HABIDO',
        '01' => 'NO HALLADO SE MUDO DE DOMICILIO',
        '02' => 'NO HALLADO FALLECIO',
        '03' => 'NO HALLADO NO EXISTE DOMICILIO',
        '04' => 'NO HALLADO CERRADO',
        '05' => 'NO HALLADO NRO.PUERTA NO EXISTE',
        '06' => 'NO HALLADO DESTINATARIO DESCONOCIDO',
        '07' => 'NO HALLADO RECHAZADO',
        '08' => 'NO HALLADO OTROS MOTIVOS',
        '09' => 'PENDIENTE',
        '10' => 'NO APLICABLE',
        '11' => 'POR VERIFICAR',
        '12' => 'NO HABIDO',
        '20' => 'NO HALLADO',
        '21' => 'NO EXISTE LA DIRECCION DECLARADA',
        '22' => 'DOMICILIO CERRADO',
        '23' => 'NEGATIVA RECEPCION X PERSONA CAPAZ',
        '24' => 'AUSENCIA DE PERSONA CAPAZ',
        '25' => 'NO APLICABLE X TRAMITE DE REVERSION',
        '40' => 'DEVUELTO'
    ];

    public function __construct()
    {
        $this->client = new Client([
            // 'base_uri' => 'https://www.sunat.gob.pe/ol-ti-itconsultaunificadalibre/consultaUnificadaLibre/',
            'base_uri' => 'https://ww1.sunat.gob.pe/ol-ti-itconsultaunificadalibre/consultaUnificadaLibre/',
            'defaults' => [
                'exceptions' => false,
                'allow_redirects' => false
            ]
        ]);
//        $this->client = new Client(['cookies' => true]);
    }

    public function search($company_number, $document_type_id, $series, $number, $date_of_issue, $total)
    {
//        dd('aca');
        try {
            $captcha = trim($this->getCaptchaImage());
            $form_params = [
                'numRuc' => $company_number,
                'codComp' => $document_type_id,
                'numeroSerie' => $series,
                'numero' => $number,
                'fechaEmision' => Carbon::parse($date_of_issue)->format('d/m/Y'),
                'codDocRecep' => '',
                'numDocRecep' => '',
                'monto' => $total,
                'codigo' => $captcha
            ];

            $response = $this->client->request('POST', self::URL_CONSULT, [
                'curl' => [
                    CURLOPT_HTTPHEADER => [],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_COOKIEFILE => public_path('cookie.txt'),
                    CURLOPT_COOKIEJAR => public_path('cookie.txt'),
                    CURLOPT_POSTFIELDS => http_build_query($form_params)
                ],
                'http_errors' => false,
                'headers' => [
                    'Accept' => 'application/json',
                ],
//                'form_params' => $form_params
            ]);

            $html = $response->getBody()->getContents();
            $response = json_decode(json_decode($html));
            if($response->rpta === 1) {
                return [
                    'success' => true,
                    'response' => $response,
                    'data' => [
                        'comprobante_estado_codigo' => $response->data->estadoCp,
                        'comprobante_estado_descripcion' => $this->document_state[$response->data->estadoCp],
                        'empresa_estado_codigo' => $response->data->estadoRuc,
                        'empresa_estado_description' => $this->company_state[$response->data->estadoRuc],
                        'empresa_condicion_codigo' => $response->data->condDomiRuc,
                        'empresa_condicion_descripcion' => $this->company_condition[$response->data->condDomiRuc],
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response->data
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getCaptchaImage()
    {
//        $response = $this->client->request('GET', self::URL_CAPTCHA);

        $response = $this->client->request('GET', self::URL_CAPTCHA, [
            'curl' => [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_COOKIEFILE => public_path('cookie.txt'),
                CURLOPT_COOKIEJAR => public_path('cookie.txt'),
            ]
        ]);

//        dd($response);
        $temp = tempnam(sys_get_temp_dir(), 'captcha_');
        file_put_contents($temp, $response->getBody()->getContents());
        $ocr = new TesseractOCR($temp);
        if($this->isWindows()) {
            $ocr->executable("C:\\Program Files\\Tesseract-OCR\\tesseract.exe");
        }
        $text =  $ocr->run();

        return $text;
    }

    private function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public function validateAndChangeDocuments($month, $year)
    {
        $company = Company::first();
        for ($i = $month; $i <= $month; $i++) {
            $date = Carbon::createFromDate($year, $i, 1);
            $date_from = $date->format('Y-m-d');
            $date_to = $date->endOfMonth()->format('Y-m-d');
            $documents = Document::where('state_type_id', '01')
                ->where('soap_type_id', '02')
                ->where('document_type_id', '03')
                ->where('series', 'B146')
                ->whereBetween('date_of_issue', [$date_from, $date_to])
                ->orderBy('number')
                ->get();
            Log::info('-------------------------------------------------');
            Log::info('Periodo: ' . $date_from . ' al ' . $date_to);
            Log::info('Documentos:' . count($documents));
            foreach ($documents as $document) {
                reValidate:
                sleep(2);
                $response = $this->search($company->number,
                    $document->document_type_id, $document->series, $document->number,
                    $document->date_of_issue->format('Y-m-d'), $document->total);
                if ($response['success']) {
                    Log::info($document->series . '-' . $document->number . '|' . 'Mensaje: ' . $response['data']['comprobante_estado_descripcion']);
                    if ($response['data']['comprobante_estado_codigo'] === '1') {
                        $document->update([
                            'state_type_id' => '05'
                        ]);
                    }
                } else {
                    //Log::info($document->series.'-'.$document->number.'|'.'Mensaje: '.$response['message']);
                    goto reValidate;
                }
            }
            Log::info('-------------------------------------------------');
        }
    }
}
