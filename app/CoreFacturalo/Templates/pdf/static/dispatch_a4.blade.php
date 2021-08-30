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
    <tr>
        @if($company->logo)
            <td width="10%">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" alt="{{ $company->name }}"  class="company_logo" style="max-width: 300px">
            </td>
        @else
            <td width="10%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="50%" class="pl-3 text-center">
            <div>
                <h3 style="text-transform: uppercase;">{{ $company->name }}</h3>
                <h5 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h5>
                <h5>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h5>
                <h5>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h5>
            </div>
        </td>
        <td width="40%" class="border-box p-4 text-center">
            <h4>{{ 'RUC '.$company->number }}</h4>
            <h4 class="text-center">{{ $document->document_type->description }}</h4>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width border-box mt-10">
    <thead>
    <tr>
        <!-- <th class="border-bottom text-left">DESTINATARIOoooooo</th> -->
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="half-width border-box" >Punto de Partida _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
        <td class="half-width border-box" >Punto de LLegada _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
    </tr>
    <tr>
        <td class="border-left-right"></td>
        <td class="text-center border-left-right">Nombre o razon social del Destinatario  </td>
    </tr>
    <tr>
        <td class="border-left-right">Fecha de Inicio de traslado  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
        <td class="border-left-right"> _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
    </tr>
    <tr>
        <td class="half-width border-bottom border-left-right">Costo Minimo _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
        <td class="half-width border-bottom border-left-right">RUC _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  DNI _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td>
    </tr>
    <tr>
        <td class="text-center border-left-right">UNIDAD DE TRANSPORTES Y CONDUCTOR </td>
        <td class="text-center border-left-right">EMPRESA DE TRANSPORTES </td>
    </tr>
    <tr>
        <td class="border-left-right">Marca y numero de placa _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
        <td class="border-left-right">Razon social _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
    </tr>
    <tr>
        <td class="border-left-right">N de constancia de inscripcion  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
        <td> _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
    </tr>
    <tr>
        <td class="border-left-right">N (s) de Licencia (s) de conducir  _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
        <td class="border-left-right">RUC _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </td>
    </tr>
    </tbody>
</table>
<!-- old -->
<table class="full-width border-box mb-10">
    <thead class="">
    <tr>
        <th class="border-box text-center">Código</th>
        <th class="border-box text-center">Descripción</th>
        <th class="border-box text-center">Cantidad</th>
        <th class="border-box text-center">UNIDAD DE MEDIDA</th>
        <th class="border-box text-center">PESO TOTAL</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border-left-right height-400" ></td>
            <td class="border-left-right"></td>
            <td class="border-left-right"></td>
            <td class="border-left-right"></td>
            <td class="border-left-right"></td>
        </tr>
        <tr class="border-box">
            <td colspan="5">Tipo y Numero del Comprobante de Pago: </td>
        </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10">
    <tr>
        <td rowspan="4" class="vertical">MOTIVO DEL TRASLADO</td>
        <td>[ ] Venta</td>
        <td>[ ] Consignacion</td>
        <td>[ ] Traslado de bienes para transformacion</td>
        <td>[ ] Traslado por zona primaria</td>
        <td>[ ] Exhibicion</td>
    </tr>
    <tr>
        <td>[ ] Venta sujeta a confirmar</td>
        <td>[ ] Devolucion</td>
        <td>[ ] Recojo de bienes</td>
        <td>[ ] Importacion</td>
        <td>[ ] Entrega de uso</td>
    </tr>
    <tr>
        <td>[ ] Compra</td>
        <td>[ ] Traslado entre establecimientos de la misma empresa</td>
        <td>[ ] Traslado por emisor itinerante de comprobante de pago</td>
        <td>[ ] Exportacion</td>
        <td>[ ] Demostracion</td>
    </tr>
    <tr>
        <td colspan="3">[ ] Otros _______________________________________________________________________________</td>
        <td>[ ] Venta con entrega a terceros</td>
        <td>[ ] Traslado para propia utilizacion</td>
    </tr>
</table>
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
