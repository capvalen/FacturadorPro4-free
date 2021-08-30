<?php

namespace App\CoreFacturalo\WS\Response;

/**
 * Class StatusResult.
 */
class StatusResult extends BillResult
{
    /**
     * StatusCode enviado por Sunat.
     *
     * 0 = Procesó correctamente
     * 98 = En proceso
     * 99 = Proceso con errores
     *
     * @var string
     */
    protected $code;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}
