<?php

namespace App\Core\Services\Extras;

use App\Models\Tenant\Company;
use Carbon\Carbon;
use DiDom\Document as DiDom;
use Exception;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;
use GuzzleHttp\Client;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ValidateCpe
{
    const URL_CONSULT = 'http://www.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm';
    const URL_CAPTCHA = 'http://www.sunat.gob.pe/ol-ti-itconsvalicpe/captcha?accion=image';

    protected $company;
    protected $client;
    protected $document_type_code = [
        '01' => '03',
        '03' => '06'
    ];

    public function __construct()
    {
        $this->company = Company::first();
        $this->client = new Client(['cookies' => true]);
    }

    public function search($document_type_code, $series, $number, $date_of_issue, $total = null)
    {
        $this->getCaptchaImage();
        try {
            $captcha = trim($this->getCaptchaImage());
            $response = $this->client->request('POST', self::URL_CONSULT, [
                'form_params' => [
                    'accion' => 'CapturaCriterioValidez',
                    'num_ruc' => $this->company->number,
                    'tipocomprobante' => $this->document_type_code[$document_type_code],
                    'num_serie' => $series,
                    'num_comprob' => $number,
                    'fec_emision' => Carbon::parse($date_of_issue)->format('d/m/Y'),
                    'cantidad' => $total,
                    'codigo' => $captcha
                ]
            ]);

            $html = $response->getBody()->getContents();
            $xp = new DiDom($html);
            $sub_headings = $xp->find('td.bgn');
            if (count($sub_headings) > 0) {
                return  [
                    'success' => true,
                    'message' => $sub_headings[0]->text()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "No se obtuvo resultado de la consulta:{$series}-{$number}"
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
        $response = $this->client->request('GET', self::URL_CAPTCHA);
        $temp = tempnam(sys_get_temp_dir(), 'captcha_');
        file_put_contents($temp, $response->getBody()->getContents());
        $ocr = new TesseractOCR($temp);
        if($this->isWindows()) {
            $ocr->executable("C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe");
        }
        $text =  $ocr->run();

        return $text;
    }

    private function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}
