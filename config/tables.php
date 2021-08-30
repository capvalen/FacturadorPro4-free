<?php

return [
    'system' => [
        'state_types' => [
            '01' => 'Registrado',
            '03' => 'Enviado',
            '05' => 'Aceptado',
            '07' => 'Observado',
            '09' => 'Rechazado',
            '11' => 'Anulado',
            '13' => 'Anulando',// 'Anulación registrada',
            '15' => 'Anulando',// 'Anulación enviada',
        ],
        'soap_sends' => [
            '01' => 'Sunat',
            '02' => 'Ose',
        ],
        'soap_types' => [
            '01' => 'Demo',
            '02' => 'Producción',
        ],
        'groups' => [
            '01' => 'F',
            '02' => 'B',
        ],
        'printing_formats' => [
            'a4' => 'A4',
            'ticket' => 'Ticket'
        ]
    ],
    'tenant' => [
        'document_types' => [
            '01' => 'Factura electrónica',
            '03' => 'Boleta electrónica',
            '07' => 'Nota de crédito electrónica',
            '08' => 'Nota de débito electrónica',
        ]
    ],
];
