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
        <td width="50%" class="pl-3"></td>
        <td width="40%" class="border-box p-4 text-center">
            <h4 class="text-center">{{ $document->document_type->description }}</h4>
            <h4>{{ 'RUC '.$company->number }}</h4>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width">
    <thead>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" style="text-decoration: underline;">DATOS DEL TRASLADO</th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
    </thead>
    <tbody>
        <tr class=" mt-10">
            <td>Fecha de emisión: {{ $document->date_of_issue->format('d/m/Y') }}</td>
            <td>Fecha de traslado: {{ $document->date_of_shipping->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>Motivo de traslado: {{ $document->transfer_reason_type->description }}</td>
            <td>Modalidad de traslado: {{ $document->transport_mode_type->description }}</td>
        </tr>
        <tr>
            <td>Peso Bruto Total de la Guía: ({{ $document->unit_type_id }}) {{ $document->total_weight }} </td>
            <td>Documento: 
                @if ($document->reference_document)
                    {{$document->reference_document->document_type->description}} {{ $document->reference_document->number_full }}
                @endif
            </td>
        </tr>
    </tbody>
</table>
<table class="full-width">
    <thead>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" style="text-decoration: underline;">DATOS DEL DESTINATARIO</th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
        <tr>
            <th class="text-left" ></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Apellidos y Nombre o Razón Social: {{ $customer->name }}</td>
        </tr>
        <tr>
            <td>R.U.C. / DNI: {{ $customer->number }}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td>Punto de partida: </td>
            <td>Punto de llegada: </td>
        </tr>
        <tr>
            <td class="border-box half-width">{{ $document->origin->location_id }} - {{ $document->origin->address }}</td>
            <td class="border-box half-width">{{ $document->delivery->location_id }} - {{ $document->delivery->address }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width mt-10">
    <thead>
        <tr>
            <th class="text-left"  style="text-decoration: underline;">DATOS DEL TRANSPORTE</th>
            <th class="text-left"  style="text-decoration: underline;">DATOS DEL CONDUCTOR</th>
            <th class="text-left" style="text-decoration: underline;">DATOS DEL TRANSPORTISTA</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border-box">Placa: {{ $document->license_plate }}</td>
            {{-- @if($document->driver->license) --}}
            <td class="border-box">Licencia: {{ $document->driver->license }}</td>
            {{-- @else --}}
             {{-- @endif --}}
            <td class="border-box">Empresa: {{ $document->dispatcher->name }}</td>
        </tr>
        <tr>
            <td class="border-box">Placa semirremolque: {{ $document->secondary_license_plates ? $document->secondary_license_plates->semitrailer:''}}</td>
            {{-- <td class="border-box">Marca:</td> --}}
            <td class="border-box">Conductor: {{ $document_type_driver->description }} {{ $document->driver->number }}</td>
            <td class="border-box">{{ $document_type_dispatcher->description }}: {{ $document->dispatcher->number }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width mt-10 mb-10">
    <thead class="">
        <tr>
            <th class="border-top-bottom text-center" class="cell-solid" width="10%" >Código</th>
            <th class="border-top-bottom text-left" class="cell-solid" width="60%">Descripción del Articulo</th>
            <th class="border-top-bottom text-center" class="cell-solid" width="10%">Unidad</th>
            {{-- <th class="border-top-bottom text-center" class="cell-solid" width="10%">Unidad de medida</th> --}}
            <th class="border-top-bottom text-right" class="cell-solid" width="10%">Cantidad</th>
            <th class="border-top-bottom text-center" class="cell-solid" width="10%">Peso Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document->items as $row)

            @php
                $total_weight_line = 0;
            @endphp

            <tr>
                <td class="text-center" class="cell-solid" align="center" style="padding: 3px !important; margin-left: 5px !important">{{ $row->item->internal_id }}</td>
                <td class="text-left" class="cell-solid" style="padding: 3px !important; margin-left: 5px !important">{{ $row->item->description }}</td>
                <td class="text-left" class="cell-solid" align="center" style="padding: 3px !important; margin-left: 5px !important">{{ $row->item->unit_type_id }}</td>
                <td class="text-right" class="cell-solid" align="right" style="padding: 3px !important; margin-left: 5px !important">
                    @if(((int)$row->quantity != $row->quantity))
                        {{ $row->quantity }}
                    @else
                        {{ number_format($row->quantity, 0) }}
                    @endif
                </td>
                @if($row->relation_item->attributes)
                    @foreach($row->relation_item->attributes as $attr)
                        @if($attr->attribute_type_id === '5032')
                        @php
                            $total_weight_line += $attr->value * $row->quantity;  
                        @endphp
                        @endif
                    @endforeach
                @endif
                <td class="text-right" class="cell-solid-rl" align="right" style="padding: 3px !important; margin-left: 5px !important">{{$total_weight_line}}</td>
            </tr>

            <tr>
                <td class="text-center"></td>
                @if($row->relation_item->name)
                <td class="text-left" colspan="4" class="cell-solid" style="padding: 3px !important;">{{ $row->relation_item->name }}</td> 
                @else
                <td class="text-left" colspan="4" class="cell-solid" style="padding: 3px !important;"><p style="color:#ffffff !important">X</p></td> 
                @endif
            </tr>

            <tr>
                <td class="text-center"></td>
                <td class="text-left" colspan="4" class="cell-solid" style="padding: 3px !important;"><p style="color:#ffffff !important">X</p></td> 
            </tr>
        @endforeach
    </tbody>
</table>
<table class="full-width mt-10 mb-10">
    <tr>
        <td width="60%">
            <h5 class="font-bold">OBSERVACION</h5>
            <h5>{{ $document->observations }}</h5>
            <h5 class="text-center">____________________________________________________________________</h5>
        </td>
        <td width="40%" class="border-box p-4 text-center" rowspan="2">
            <h4 class="text-center">___________________________________</h4>
            <h4>___________________________________</h4>
            <h5 class="text-center">CONFORMIDAD DEL CLIENTE</h5>
            <h5>DNI ______________________________________</h5>
        </td>
    </tr>
    <tr>
        <td width="60%">
            <h5>El documento electrónico ha sido aceptado</h5>
            <h5></h5>
            <h5 class="text-center">____________________________________________________________________</h5>
        </td>
    </tr>
    <tr>
        <td width="60%">
            <h5>Representación impresa de la Guía de Remisión Electrónica Remitente</h5>
            <h5></h5>
            <h5 class="text-center">____________________________________________________________________</h5>
        </td>
    </tr>
</table>
<table class="full-width mt-10 mb-10">
    <tr>
        <td width="25%">
            Usuario: {{ $document->user->name }}
        </td>
        <td width="17%">
            Fecha: {{ date('d-m-Y')}}
        </td>
        <td width="17%">
            Hora: {{ date('H:i:s')}}
        </td>
        <td width="41%">
        </td>
    </tr>
</table>


</body>
</html>
