<?php
namespace App\CoreFacturalo\Helpers\Certificate;

use App\CoreFacturalo\WS\Signed\Certificate\X509Certificate;
use App\CoreFacturalo\WS\Signed\Certificate\X509ContentType;

class GenerateCertificate
{
    public static function typePEM($pfx, $password)
    {
        $certificate = new X509Certificate($pfx, $password);
        return $certificate->export(X509ContentType::PEM);
    }
}