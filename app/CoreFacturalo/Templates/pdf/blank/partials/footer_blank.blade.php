@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
@endphp
<head>
    <link href="{{ $path_style }}" rel="stylesheet" />
</head>
<body>

<table class="full-width pt-5">
    <tr>
        <td class="text-bold font-bold">OBSERVACIONES</td>
        <td class="text-bold font-bold">N° DE BULTOS: {{ $document->packages_number != null ? $document->packages_number : '' }}</td>
    </tr>
    <tr colspan="2">
        <td style="line-height: 20px;">{{ $document->observations ? $document->observations : '-' }}</td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td width="26%"></td>
        <td width="44%" class="text-bold font-bold">
            @if ($document->reference_document)
                @if($document->reference_document)
                {{$document->reference_document->document_type->description}} {{ ($document->reference_document) ? $document->reference_document->number_full : "" }}
                @endif
            @endif
        </td>
        <td class="text-bold font-bold" style="line-height: 20px;">
            PESO BRUTO TOTAL ({{ $document->unit_type_id }}):{{ $document->total_weight }}
        </td>
    </tr>
</table>
<table class="full-width mb-3 mt-3">
    <tr>
        <td width="6.5%"><br></td>
        <td width="2%">{{ $document->transfer_reason_type->description == 'Venta' ? 'X' : '' }}</td>
        <td width="14%"></td>
        <td width="2%"></td>
        <td width="18.5%"></td>
        <td width="2%"></td>
        <td width="23%"></td>
        <td width="2%">{{ $document->transfer_reason_type->description == 'Traslado a zona primaria' ? 'X' : '' }}</td>
        <td width="16.5%"></td>
        <td width="2%"></td>
        <td></td>
    </tr>
    <tr>
        <td><br></td>
        <td>{{ $document->transfer_reason_type->description == 'Venta sujeta a confirmación del comprador' ? 'X' : '' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $document->transfer_reason_type->description == 'Importación' ? 'X' : '' }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td><br></td>
        <td>{{ $document->transfer_reason_type->description == 'Compra' ? 'X' : '' }}</td>
        <td></td>
        <td>{{ $document->transfer_reason_type->description == 'Traslado entre establecimientos de la misma empres...' ? 'X' : '' }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $document->transfer_reason_type->description == 'Exportación' ? 'X' : '' }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</body>