@php
    $establishment = $document->establishment;
    $supplier = $document->supplier;
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="10%">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" alt="{{ $company->name }}" class="company_logo" style="max-width: 200px">
            </td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h3 class="">{{ $company->name }}</h3>
                <h4>{{ 'RUC '.$company->number }}</h4>
                <h5>{{ ($establishment->address !== '-')? $establishment->address : '' }}</h5>
                <h5>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h5>
                <h5>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h5>
            </div>
        </td>
        <td width="40%" class="border-box p-3 text-center">
            <h4 class="text-center">{{ $document->document_type->description }}</h4>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="25%">Señor(es):</td>
        <td width="45%">{{ $supplier->name }}</td>
        <td width="15%">Fecha de emisión:</td>
        <td width="15%">{{ $document->date_of_issue->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td>{{ $supplier->identity_document_type->description }}:</td>
        <td>{{ $supplier->number }}</td>
        <td>Moneda:</td>
        <td>{{ $document->currency_type_id }}</td>
    </tr>
    <tr>
        <td>Dirección:</td>
        <td colspan="3">{{ $supplier->address }}</td>
    </tr>
    <tr>
        <td>Régimen de retención:</td>
        <td colspan="3">{{ $document->retention_type->description }}</td>
    </tr>
</table>
<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center desc">Tipo<br/>Comprobante</th>
        <th class="border-top-bottom text-center desc">Número<br/>Comprobante</th>
        <th class="border-top-bottom text-center desc">Fecha de<br/>Emisión</th>
        <th class="border-top-bottom text-center desc">Moneda<br/>Comprobante</th>
        <th class="border-top-bottom text-center desc">Total<br/>Comprobante</th>
        <th class="border-top-bottom text-center desc">Tasa %</th>
        <th class="border-top-bottom text-center desc">Importe<br/>Retención</th>
        <th class="border-top-bottom text-center desc">Tipo<br/>Cambio</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->documents as $row)
        <tr>
            <td class="text-center">{{ $row->document_type->short }}</td>
            <td class="text-center">{{ $row->series }}-{{ $row->number }}</td>
            <td class="text-center">{{ $row->date_of_issue->format('d/m/Y') }}</td>
            <td class="text-center">{{ $row->currency_type_id }}</td>
            <td class="text-right">{{ $row->total_document }}</td>
            <td class="text-center">{{ $document->retention_type->percentage }}</td>
            <td class="text-right">{{ $row->total_retention }}</td>
            <td class="text-right">{{ $row->exchange_rate->factor }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td class="border-top text-right" colspan="4">Totales({{ $document->currency_type->symbol }})</td>
        <td class="border-top text-right">{{ $document->total }}</td>
        <td class="border-top"></td>
        <td class="border-top text-right">{{ $document->total_retention }}</td>
        <td class="border-top"></td>
    </tr>
    </tfoot>
</table>
<table class="full-width">
    @if($document->hash)
    <tr>
        <td>Código Hash: {{ $document->hash }}</td>
    </tr>
    @endif
    @foreach($document->legends as $row)
        <tr>
            <td class="font-bold">{{ $row->value }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>