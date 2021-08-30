<?php

namespace App\CoreFacturalo\Cpe;

use App\CoreFacturalo\Helpers\StorageDocument;
use App\Models\Tenant\Company;
use Greenter\Ws\Services\SoapClient;
use Greenter\Ws\Services\SunatEndpoints;

class ConsultCdrService
{
    use StorageDocument;

    protected $company;
    protected $service;
    protected $cdr = false;

    public function __construct()
    {
        $this->company = Company::first();
        $username = $this->company->soap_username;
        $password = $this->company->soap_password;

        $ws = new SoapClient(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');
        $ws->setCredentials($username, $password);
        $this->service = new \Greenter\Ws\Services\ConsultCdrService();
        $this->service->setClient($ws);
    }

    public function getStatusCdr(...$parameters)
    {
        $this->cdr = true;
        return $this->getStatus(...$parameters);
    }

    public function getStatus($document_type_code, $series, $number)
    {
        $arguments = [
            $this->company->number,
            $document_type_code,
            $series,
            intval($number)
        ];

        if ($this->cdr) {
            $result = $this->service->getStatusCdr(...$arguments);
            if ($result->getCdrZip()) {
                $filename = implode('-', $arguments);
                $this->uploadStorage('cdr', $result->getCdrZip(), "R-{$filename}", 'zip');
            }
        } else {
            $result = $this->service->getStatus(...$arguments);
        }

        if ($result->isSuccess()) {
            $data = [
                'code' => $result->getCode(),
                'message' => $result->getMessage(),
            ];
            if (!is_null($result->getCdrResponse())) {
                $data['cdrResponse'] = [
                    'description' => $result->getCdrResponse()->getDescription(),
                    'notes' => $result->getCdrResponse()->getNotes()
                ];
            }
            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            return [
                'success' => false,
                'message' => $result->getError()->getMessage(),
            ];
        }
    }

}