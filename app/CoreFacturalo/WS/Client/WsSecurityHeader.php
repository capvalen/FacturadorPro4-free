<?php

namespace App\CoreFacturalo\WS\Client;

use SoapHeader;
use SoapVar;

/**
 * Class WSSESecurityHeader.
 */
class WsSecurityHeader extends SoapHeader
{
    const WSS_NAMESPACE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    const PASSWORD_TYPE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText';
    const PASSWORD_FORMAT = '<o:Password xmlns:o="%s" Type="%s">%s</o:Password>';

    public function __construct($username, $password)
    {

        $header_password = sprintf(self::PASSWORD_FORMAT, self::WSS_NAMESPACE, self::PASSWORD_TYPE, $password);

        $security = new SoapVar(
            [new SoapVar(
                [
                    new SoapVar($username, XSD_STRING, null, null, 'Username', self::WSS_NAMESPACE),
                    new SoapVar($header_password, XSD_ANYXML),
                    // new SoapVar($password, XSD_STRING, null, null, 'Password', self::WSS_NAMESPACE),
                ],
                SOAP_ENC_OBJECT,
                null,
                null,
                'UsernameToken',
                self::WSS_NAMESPACE
            )],
            SOAP_ENC_OBJECT
        );
        $this->SoapHeader(self::WSS_NAMESPACE, 'Security', $security, false);
    }
}
