<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Envio de Comprobante de Pago Electrónico</title>
    <style>
        body {
            color: #000;
        }
        ul {
            list-style: none;
        }
    </style>
</head>
<body>
<p>Estimad@: 
    @if($document->customer)
        {{ $document->customer->name }}
    @else
        {{ $document->supplier->name }}
    @endif
    , informamos que su comprobante electrónico ha sido emitido exitosamente.</p>
<p>Los datos de su comprobante electrónico son:</p>
<ul>
    <li>Razon social: {{ $company->name }}</li>
    <li>Teléfono: {{ $document->establishment->telephone }}</li>
{{--    <li>Tipo de comprobante: {{ $document->document_type->description }}</li>--}}
    <li>Fecha de emisión: {{ $document->date_of_issue->format('d/m/Y') }}</li>
    <li>Nro. de comprobante: {{ $document->series.'-'.$document->number }}</li>
    <li>Total: {{ $document->total }}</li>
</ul>
</body>
</html>