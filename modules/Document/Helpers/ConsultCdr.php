<?php

namespace Modules\Document\Helpers;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\WS\Services\ConsultCdrService;
use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\WS\Validator\XmlErrorCodeProvider;
use App\CoreFacturalo\WS\Client\WsClient;
use App\CoreFacturalo\WS\Services\SunatEndpoints;
use App\Models\Tenant\Company;
use Exception;


class ConsultCdr
{
    
    protected $wsClient;
    protected $document;
    protected $response;
    use StorageDocument;
    const REGISTERED = '01';
    const SENT = '03';
    const ACCEPTED = '05';
    const OBSERVED = '07';
    const REJECTED = '09';
    const CANCELING = '13';
    const VOIDED = '11';


    public function search($document){

        $this->document = $document;
        $document_type_id = $this->document->document_type_id;
        $series = $this->document->series;
        $number = $this->document->number;

        $wsdl = 'consultCdrStatus';
        $company = Company::active();
        $username = $company->soap_username;
        $password = $company->soap_password;

        $company_number = $company->number;

        $this->wsClient = new WsClient($wsdl);
        $this->wsClient->setCredentials($username, $password);
        $this->wsClient->setService(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');

        $consultCdrService = new ConsultCdrService();
        $consultCdrService->setClient($this->wsClient);
        $consultCdrService->setCodeProvider(new XmlErrorCodeProvider());
        $res = $consultCdrService->getStatusCdr($company_number,$document_type_id,$series,$number);

        if(!$res->isSuccess()) {

            return [
                'success' => false,
                'message' => "Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}"
            ];
            // throw new Exception("Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}");
            
        } else {

            $cdrResponse = $res->getCdrResponse();
            $code = $cdrResponse->getCode();
            $description = $cdrResponse->getDescription();

            // dd($cdrResponse, $res->getCdrZip());
            // $this->updateState(self::ACCEPTED);

            $this->response = [
                'sent' => true,
                'code' => $cdrResponse->getCode(),
                'description' => $cdrResponse->getDescription(),
                'notes' => $cdrResponse->getNotes()
            ];

            $this->validationCodeResponse($code, $description, $res);

            return [
                'success' => true,
                'message' => $description
            ];

        }

    }

    
    public function validationCodeResponse($code, $message, $res)
    {
        //Errors
        if($code === 'ERROR_CDR') {
            return;
        }

        if($code === 'HTTP') {
            throw new Exception("Code: {$code}; Description: {$message}");
        }

        if((int)$code === 0) {

            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::ACCEPTED);
            }

            return;
        }

        if((int)$code < 2000) {
            //Excepciones
            throw new Exception("Code: {$code}; Description: {$message}");

        } elseif ((int)$code < 4000) {
            //Rechazo
            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::REJECTED);
            }

        } else {

            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::OBSERVED);
            }
            //Observaciones
        }
        return;
    }


    public function uploadFile($file_content, $file_type)
    {
        $this->uploadStorage($this->document->filename, $file_content, $file_type);
    }


    public function updateState($state_type_id)
    {
        $this->document->update([
            'state_type_id' => $state_type_id,
            'soap_shipping_response' => isset($this->response['sent']) ? $this->response:null
        ]);
    }

}
