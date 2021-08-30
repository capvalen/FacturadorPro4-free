@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);
    $document_type_dispatcher = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->dispatcher->identity_document_type_id);

@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<table class="full-width">
    <!-- <thead> -->
        <tr>
            @if($company->logo)
                <td class="text-center" width="57.5%">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" alt="{{ $company->name }}"  class="company_logo" style="max-width: 300px">
                </td>
            @else
                <td class="text-center" width="57.5%">
                    {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
                </td>
            @endif
                <td width="2.5%"></td>
            <!-- <td width="50%" class="pl-3">
                <div class="text-left">
                    <h3 class="">{{ $company->name }}</h3>
                    <h5 style="text-transform: uppercase;">
                        {{ ($establishment->address !== '-')? $establishment->address : '' }}
                        {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                        {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                        {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                    </h5>
                    <h5>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h5>
                    <h5>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h5>
                </div>
            </td> -->
            <td width="40%" rowspan="5" class="border-box p-4 text-center">
                <h4>{{ 'RUC '.$company->number }}</h4>
                <h4 class="text-center">{{ $document->document_type->description }}</h4>
                <h3 class="text-center">{{ $document_number }}</h3>
            </td>
        </tr>
        <tr>
            <td class="text-center border-bottom"><h3>{{ $company->name }}</h3></td>
        </tr>
        <tr>
            <td class="text-center"><h5 style="text-transform: uppercase;">
                        {{ ($establishment->address !== '-')? $establishment->address : '' }}
                        {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                        {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                        {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                    </h5></td>
        </tr>
        <tr>
            <td class="text-center"><h5>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h5></td>
        </tr>
        <tr>
            <td class="text-center"><h5>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h5></td>
        </tr>
    <!-- </thead> -->
    <tr>
        <td>Motivo Traslado: {{ $document->transfer_reason_type->description }}</td>
        <td></td>
        <td>Fecha Emisión: {{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
</table>
<!-- <table class="full-width border-box mt-10 mb-10">
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
        <td>RUC: {{ $customer->number }}
                 {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                 {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                 {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
        </td>
    </tr>
    <tr>
        <td>Dirección: {{ $customer->address }}</td>
    </tr>
    </tbody>
</table> -->
<!-- <table class="full-width border-box mt-10 mb-10">
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
        <td>P.Partida: {{ $document->origin->location_id }} - {{ $document->origin->address }}</td>
        <td>P.Llegada: {{ $document->delivery->location_id }} - {{ $document->delivery->address }}</td>
    </tr>
    </tbody>
</table> -->
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left">PUNTO DE PARTIDA</th>
        <th class="border-bottom text-left">PUNTO DE LLEGADA</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Fecha Inicio de Traslado: {{ $document->date_of_shipping->format('Y-m-d') }}</td>
        <td>Razón Social: {{ $document->customer->name }}</td>
    </tr>
    <tr>
        <td>Dirección: {{ $document->origin->location_id }} - {{ $document->origin->address }} </td>
        <td>{{$document->customer->identity_document_type->description}}: {{ $document->customer->number }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Dirección: {{ $document->delivery->location_id }} - {{ $document->delivery->address }}</td>
    </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead>
    <tr>
        <th class="border-bottom text-left">TRANSPORTE</th>
        <th class="border-bottom text-left">CONDUCTOR</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Nombre y/o razón social: {{ $document->dispatcher->name }}</td>
        <td>{{$document_type_driver->description}}: {{ $document->driver->number }}</td>
    </tr>
    <tbody>
    <tr>
        <td>{{ $document_type_dispatcher->description }}: {{ $document->dispatcher->number }}</td>
        @if($document->driver->license)
            <td>Licencia del conductor: {{ $document->driver->license }}</td>
        @endif
    </tr>
    <tr>
        <td>Modalidad de Transporte: {{ $document->transport_mode_type->description }}</td>
    </tr>
    <tr>
        @if($document->secondary_license_plates)
            @if($document->secondary_license_plates->semitrailer)
                <td>Número de placa semirremolque: {{ $document->secondary_license_plates->semitrailer }}</td>
            @endif
        @endif
        <td>Número de placa del vehículo: {{ $document->license_plate }}</td>
    </tr>
</table>
<table class="full-width border-box mt-10 mb-10">
    <thead class="">
    <tr class="bg-green">
        <th class="border-top-bottom text-center text-white">Item</th>
        <th class="border-top-bottom text-center text-white">Código</th>
        <th class="border-top-bottom text-left text-white">Descripción</th>
        <th class="border-top-bottom text-center text-white">Unidad</th>
        <th class="border-top-bottom text-right text-white">Cantidad</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td class="text-center">{{ $row->item->internal_id }}</td>
            <td class="text-left">{{ $row->item->description }}</td>
            <td class="text-center">{{ $row->item->unit_type_id }}</td>
            <td class="text-right">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<table class="full-width">
    <tbody>
        <tr>
            <td class="text-right">Peso Bruto Total({{ $document->unit_type_id }}): {{ $document->total_weight }}</td>
            <td class="text-right">Número de Bultos: {{ $document->packages_number }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width mt-10 mb-10">
    <tr>
        <td colspan="1" class="half-width text-bold border-bottom font-bold">OBSERVACIONES</td>
    </tr>
    <tr>
        <td rowspan="8" class="border-box">{{ $document->observations }}</td>
        <td width="1.5%"></td>
        <td>Autorizado por la SUNAT</td>
    </tr>
    <tr>
        <td width="1.5%"></td>
        <td>Representacion impresa de {{ $document->document_type->description }}</td>
    </tr>
</table>
<!-- <table class="half-width float-left">
    <tr>
        <td>Autorizado por la SUNAT</td>
    </tr>
    <tr>
        <td>Representacion impresa de {{ $document->document_type->description }}</td>
    </tr>
</table> -->

@if ($document->reference_document)
<table class="full-width border-box">
    @if($document->reference_document)
    <tr>
        <td class="text-bold border-bottom font-bold">{{$document->reference_document->document_type->description}}</td>
    </tr>
    <tr>
        <td>{{ ($document->reference_document) ? $document->reference_document->number_full : "" }}</td>
    </tr>
    @endif
</table>
@endif

@if ($document->reference_order_form_id)
<table class="full-width border-box">
    @if($document->order_form)
    <tr>
        <td class="text-bold border-bottom font-bold">ORDEN DE PEDIDO</td>
    </tr>
    <tr>
        <td>{{ ($document->order_form) ? $document->order_form->number_full : "" }}</td>
    </tr>
    @endif
</table>
@endif

</body>
</html>
