<?php

namespace App\CoreFacturalo\WS\Response;

/**
 * Class SummaryResult.
 */
class SummaryResult extends BaseResult
{
    /**
     * @var string
     */
    protected $ticket;

    /**
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param string $ticket
     *
     * @return SummaryResult
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }
}
