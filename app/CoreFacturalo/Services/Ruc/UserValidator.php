<?php

namespace App\CoreFacturalo\Services\Ruc;

use App\CoreFacturalo\Services\Http\ClientInterface;

/**
 * Class UserValidator
 * @package Peru\Sunat
 */
class UserValidator
{
    const URL_VALIDEZ = 'http://www.sunat.gob.pe/cl-ti-itestadousr/usrS00Alias';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * UserValidator constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Consulta válidez del usuario SOL.
     *
     * @param string $ruc
     * @param string $user
     * @return bool
     */
    public function valid($ruc, $user)
    {
        $this->client->get(self::URL_VALIDEZ);
        $html = $this->client->post(self::URL_VALIDEZ, [
            'accion' => 'e1',
            'ruc' => $ruc,
            'usr' => $user,
        ]);

        $state = $this->getStatus($html);

        return strpos(strtoupper($state), 'ACTIVO') !== false;
    }

    private function getStatus($html)
    {
        $xpt = HtmlParser::getXpathFromHtml($html);
        $nodes = $xpt->query('//strong');

        if ($nodes->length !== 1) {
            return '';
        }

        return $nodes->item(0)->nodeValue;
    }
}