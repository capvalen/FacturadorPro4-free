@php
    $establishment = $document->establishment;
    $customer = $document->customer;
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
    {{--<tr><td colspan="2" style="height: 10px"></td></tr>--}}
    <tr>
        {{--@if($company->logo)--}}
        {{--<td width="20%">--}}
        {{--<div class="company_logo_box">--}}
        {{--<img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64,--}}
        {{--{{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}"--}}
        {{--alt="{{$company->name}}"--}}
        {{--class="company_logo"--}}
        {{--style="max-width: 150px;">--}}
        {{--</div>--}}
        {{--</td>--}}
        {{--@else--}}
        {{--<td width="20%">--}}
        {{--<img src="{{ public_path("storage/uploads/logos/{$company->logo}") }}" class="company_logo" style="max-width: 150px">--}}
        {{--</td>--}}
        {{--@endif--}}
        <td width="70%">
            <div class="company_logo_box">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
            </div>
        </td>
        {{--<td width="50%" class="pl-3">--}}
        {{--<div class="text-left">--}}
        {{--<h4 class="">{{ $company->name }}</h4>--}}
        {{--<h5>{{ 'RUC '.$company->number }}</h5>--}}
        {{--<h6>{{ ($establishment->address !== '-')? $establishment->address : '' }}</h6>--}}
        {{--<h6>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h6>--}}
        {{--<h6>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h6>--}}
        {{--</div>--}}
        {{--</td>--}}
        <td width="30%" class="py-4 px-2 text-center" style="border: thin solid #ccc;">
            <h4>{{ 'RUC '.$company->number }}</h4>
            <br/>
            <h5 class="text-center">{{ $document->document_type->description }}</h5>
            <br/>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<br/>
<br/>
@if($document->optional)
    @if($document->optional->invoice_number)
    Documento: {{ $document->optional->invoice_number }}
    @endif
@endif
<br/>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left">DESTINATARIO</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Razón Social: {{ $customer->name }}</td>
    </tr>
    <tr>
        <td>RUC: {{ $customer->number }}</td>
    </tr>
    <tr>
        <td>Dirección: {{ $customer->address }}, {{ strtoupper($customer->department->description) }}-{{ strtoupper($customer->province->description) }}-{{ strtoupper($customer->district->description) }}
        </td>
        {{--<td>Dirección: {{ $customer->address }}</td>--}}
    </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left" colspan="2">ENVIO</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Fecha Emisión: {{ $document->date_of_issue->format('Y-m-d') }}</td>
        <td>Fecha Inicio de Traslado: {{ $document->date_of_shipping->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>Motivo Traslado: {{ $document->transfer_reason_type->description }}</td>
        <td>Modalidad de Transporte: {{ $document->transport_mode_type->description }}</td>
    </tr>
    <tr>
        <td>Peso Bruto Total({{ $document->unit_type_id }}): {{ $document->total_weight }}</td>
        <td>Número de Bultos: {{ $document->packages_number }}</td>
    </tr>
    <tr>
        <td colspan="2">P.Partida: {{ $document->origin->location_id }} - {{ $document->origin->address }}, {{ \App\CoreFacturalo\Templates\FunctionLocation::getLocationName($document->origin->location_id) }}</td>
    </tr>
    <tr>
        <td colspan="2">P.Llegada: {{ $document->delivery->location_id }} - {{ $document->delivery->address }}, {{ \App\CoreFacturalo\Templates\FunctionLocation::getLocationName($document->delivery->location_id) }}</td>
    </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left" colspan="2">TRANSPORTE</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Razón Social: {{ $document->dispatcher->name }}</td>
        <td>RUC: {{ $document->dispatcher->number }}</td>
    </tr>
    <tbody>
    <tr>
        <td>Número de placa del vehículo: {{ $document->license_plate }}</td>
        <td>Conductor: {{ $document->driver->number }}</td>
    </tr>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead class="">
    <tr>
        <th class="border-top-bottom text-center">Item</th>
        <th class="border-top-bottom text-center">Código</th>
        <th class="border-top-bottom text-left">Descripción</th>
        <th class="border-top-bottom text-center">Unidad</th>
        <th class="border-top-bottom text-right">Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="text-center">{{ $row->item->internal_id }}</td>
            <td class="text-left">{{ $row->item->description }}</td>
            {{--<td class="text-center">{{ $row->item->unit_type_id }}</td>--}}
            <td class="text-center">
            @php
                $unit_type_description = \App\Models\Tenant\Catalogs\UnitType::find($row->item->unit_type_id);
            @endphp
            {{ $unit_type_description->description }}
            </td>
            <td class="text-right">{{ $row->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@if($document->observations)
<table class="full-width border-box">
    <tr>
        <td class="text-bold border-bottom font-bold">OBSERVACIONES</td>
    </tr>
    <tr>
        <td>{{ $document->observations }}</td>
    </tr>
</table>
@endif
</body>
</html>
