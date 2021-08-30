<?php

namespace App\CoreFacturalo\WS\Services;

use App\CoreFacturalo\WS\Response\Error;
use App\CoreFacturalo\WS\Response\StatusResult;

/**
 * Class ExtService.
 */
class ExtService extends BaseSunat
{
    /**
     * @param string $ticket
     *
     * @return StatusResult
     */

    private $customStatusCode;

    public function getStatus($ticket)
    {
        $client = $this->getClient();
        $result = new StatusResult();

        try {
            $params = [
                'ticket' => $ticket,
            ];
            $response = $client->call('getStatus', ['parameters' => $params]);
            $status = $response->status;
            $code = $status->statusCode;
            $this->setCustomStatusCode($code);

            $result->setCode($code);

            if ($this->isPending($code)) {
                $result->setError($this->getCustomError($code));

                return $result;
            }

            if ($this->isProcessed($code)) {
                $cdrZip = $status->content;
                $result
                    ->setSuccess(true)
                    ->setCdrResponse($this->extractResponse($cdrZip))
                    ->setCdrZip($cdrZip);

                $code = $result->getCdrResponse()->getCode();
            }

            if ($this->isExceptionCode($code)) {
                $this->loadErrorByCode($result, $code);
            }
        } catch (\SoapFault $e) {
            $result->setError($this->getErrorFromFault($e));
        }

        return $result;
    }

    /**
     * @param string $code
     * @return Error
     */
    private function getCustomError($code)
    {
        $error = new Error();
        $error->setCode($code)
            ->setMessage('El procesamiento del comprobante aún no ha terminado');

        return $error;
    }

    private function isProcessed($code)
    {
        return '0' == $code || '99' == $code;
    }

    private function isPending($code)
    {
        return '98' == $code;
    }
    
    //custom
    public function setCustomStatusCode($code)
    {
        $this->customStatusCode = $code;
    }

    public function getCustomStatusCode()
    {
        return (int) $this->customStatusCode;
    }

}
