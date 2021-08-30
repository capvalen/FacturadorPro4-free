<?php

namespace App\CoreFacturalo\Services\Helpers\Http;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Get Request.
     *
     * @param string $url
     * @param array  $headers
     *
     * @return string|bool
     */
    public function get($url, array $headers = []);

    /**
     * Post Request.
     *
     * @param string $url
     * @param mixed  $data
     * @param array  $headers
     *
     * @return string|bool
     */
    public function post($url, $data, array $headers = []);
}
