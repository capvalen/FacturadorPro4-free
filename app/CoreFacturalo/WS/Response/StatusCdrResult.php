<?php

namespace App\CoreFacturalo\WS\Response;
/**
 * Class StatusCdrResult.
 */
class StatusCdrResult extends BillResult
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

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
     * @return StatusCdrResult
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return StatusCdrResult
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
