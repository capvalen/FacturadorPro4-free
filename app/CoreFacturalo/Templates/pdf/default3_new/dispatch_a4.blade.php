@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $document_type_driver = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->driver->identity_document_type_id);
    $document_type_dispatcher = App\Models\Tenant\Catalogs\IdentityDocumentType::findOrFail($document->dispatcher->identity_document_type_id);

    $allowed_items = 90;
    $quantity_items = $document->items()->count();
    $cycle_items = $allowed_items - ($quantity_items * 5);
    $total_weight = 0;

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
            <td width="20%">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" alt="{{ $company->name }}"  class="company_logo" style="max-width: 300px">
            </td>
        @else
            <td width="20%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="40%" class="pl-3">
            <div class="text-left">
                <h3 class="">{{ $company->name }}</h3>
                <h4>{{ 'RUC '.$company->number }}</h4>
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
        <td width="40%" class="border-box py-2 px-2 text-center">
            <h3 class="font-bold">{{ 'R.U.C. '.$company->number }}</h3>
            <h3 class="text-center font-bold">{{ $document->document_type->description }}</h3>
            <br>
            <h3 class="text-center font-bold">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width border-box mt-10 mb-10"> 
    <tbody >
        <tr >
            <td style="text-decoration: underline;" colspan="2" class="pl-3">DESTINATARIO</td>
        </tr>
        <tr>
            <td class="pl-3"><strong>Razón Social:</strong> {{ $customer->name }}</td>

            @if ($document->reference_document)
            <td class="pl-3"><strong>COMPROBANTE:</strong> {{$document->reference_document->document_type->description}} {{$document->reference_document->number_full}}</td>
            @else
            <td class="pl-3"></td>
            @endif
        </tr>

        <tr>
            <td class="pl-3"><strong>RUC:</strong> {{ $customer->number }}</td>
            @if ($document->reference_document)
                @if ($document->reference_document->purchase_order)
                <td class="pl-3"><strong>O. COMPRA:</strong> {{$document->reference_document->purchase_order}}</td>
                @else
                <td class="pl-3"></td>
                @endif
            @else
            <td class="pl-3"></td>
            @endif
        </tr>
        <tr>
            <td colspan="2" class="pl-3"><strong>Dirección:</strong> {{ $customer->address }}
                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
            </td>
        </tr>
    </tbody>
</table>
<table class="full-width border-box mt-10 mb-10"> 
    <tbody>
        <tr>
            <td style="text-decoration: underline;" colspan="2" class="pl-3">ENVIO</td>
        </tr>
        <tr>
            <td class="pl-3"><strong>Fecha Emisión:</strong> {{ $document->date_of_issue->format('d/m/Y') }}</td>
            <td rowspan="2">
                <p style="text-decoration: underline;"><strong>PUNTO DE PARTIDA</strong></p>
                <label>
                    <strong>Dirección:</strong> {{ $document->origin->address }} - {{ $document->origin->location_id }}
                </label>
            </td>
        </tr>
        <tr>
            <td class="pl-3"><strong>Fecha de Traslado:</strong> {{ $document->date_of_shipping->format('d/m/Y') }}</td>
        </tr>

        <tr>
            <td class="pl-3"><strong>Motivo Traslado:</strong> {{ $document->transfer_reason_type->description }}</td>
            <td rowspan="2">
                <p style="text-decoration: underline;"><strong>PUNTO DE LLEGADA</strong></p>
                <label>
                    <strong>Dirección:</strong> {{ $document->delivery->address }} - {{ $document->delivery->location_id }}
                </label>
            </td>
        </tr>

        <tr>
            <td class="pl-3"><strong>Modalidad de Transporte:</strong> {{ $document->transport_mode_type->description }}</td>
        </tr>



        {{-- <tr>
            <td>Peso Bruto Total({{ $document->unit_type_id }}): {{ $document->total_weight }}</td>
            <td>Número de Bultos: {{ $document->packages_number }}</td>
        </tr>
        <tr>
            <td>P.Partida: {{ $document->origin->location_id }} - {{ $document->origin->address }}</td>
            <td>P.Llegada: {{ $document->delivery->location_id }} - {{ $document->delivery->address }}</td>
        </tr> --}}
    </tbody>
</table>

<table class="full-width border-box mt-10 mb-10"> 
    <tr>
        <td width="45%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td style="text-decoration: underline;" colspan="2"><strong>UNIDAD DE TRANSPORTE - CONDUCTOR</td>
                </tr>
                <tr>
                    <td><strong>N° Doc:</strong> {{ $document->driver->identity_document_type_id }}: {{ $document->driver->number }}</td>
                </tr>
                <tr>
                    <td><strong>Placa N°:</strong> {{ $document->license_plate }}</td>
                </tr>
                <tr>
                    <td><strong>N° Licencia:</strong> {{ $document->driver->license }}</td>
                </tr>
            </table>
        </td>
        <td width="3%"></td>

        <td width="50%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td style="text-decoration: underline;" colspan="2"><strong>EMPRESA DE TRANSPORTE</strong></td>
                </tr>
                <tr>
                    <td><strong>Transportista:</strong> {{ $document->dispatcher->name }}</td>
                </tr>
                <tr>
                    <td><strong>{{ $document_type_dispatcher->description }}:</strong> {{ $document->dispatcher->number }}</td>
                </tr>
                <tr>
                    <td></td>
                    {{-- <td>Dirección: {{ $document->driver->license }}</td> --}}
                </tr>
            </table>
        </td>

    </tr>
</table>

 
<table class="full-width mt-0 mb-0" >
    <thead >
        <tr class="">
            <th class="border-top-bottom text-center py-1 desc" class="cell-solid"  width="8%">ITEM</th>
            <th class="border-top-bottom text-center py-1 desc" class="cell-solid"  width="12%">CÓDIGO</th>
            <th class="border-top-bottom text-center py-1 desc" class="cell-solid"  width="8%">CANTIDAD</th>
            <th class="border-top-bottom text-center py-1 desc" class="cell-solid"  width="8%">U.M.</th>
            <th class="border-top-bottom text-center py-1 desc" class="cell-solid"  width="40%">DESCRIPCIÓN</th>
            <th class="border-top-bottom text-right py-1 desc" class="cell-solid"  width="12%">PESO</th>
        </tr>
    </thead>
    <tbody class=""> 
        @foreach($document->items as $row)
            @php
                $total_weight_line = 0;
            @endphp
            <tr>
                <td class="p-1 text-center align-top desc cell-solid-rl">{{ $loop->iteration }}</td>
                <td class="p-1 text-center align-top desc cell-solid-rl">{{ $row->item->internal_id }}</td>
                <td class="p-1 text-center align-top desc cell-solid-rl">
                    @if(((int)$row->quantity != $row->quantity))
                        {{ $row->quantity }}
                    @else
                        {{ number_format($row->quantity, 0) }}
                    @endif
                    
                </td>
                <td class="p-1 text-center align-top desc cell-solid-rl">{{ $row->item->unit_type_id }}</td>
                <td class="p-1 text-left align-top desc text-upp cell-solid-rl">
                    {!!$row->item->description!!}
                    @if($row->relation_item->attributes)
                        @foreach($row->relation_item->attributes as $attr)
                            @if($attr->attribute_type_id === '5032')
                            @php
                                $total_weight += $attr->value * $row->quantity;  
                                $total_weight_line += $attr->value * $row->quantity;  
                                  
                            @endphp
                            @endif
                            <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                        @endforeach
                    @endif
                </td> 
                <td class="p-1 text-center align-top desc cell-solid-rl">
                    {{ $total_weight_line }}
                </td>
            </tr>

        @endforeach

        @for($i = 0; $i < $cycle_items; $i++)
        <tr>
            <td class="p-1 text-center align-top desc cell-solid-rl"></td>
            <td class="p-1 text-center align-top desc cell-solid-rl"></td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
        </tr>
        @endfor
        <tr>
            <td class="cell-solid-offtop"></td>
            <td class="cell-solid-offtop"></td>
            <td class="cell-solid-offtop"></td>
            <td class="cell-solid-offtop"></td>
            <td class="cell-solid-offtop"></td>
            <td class="cell-solid-offtop"></td>
        </tr>
    </tbody>
</table>
 

<table class="full-widthmt-10 mb-10"> 
    <tr>
        <td width="75%">
            <table class="full-width">
                <tr>
                    @php
                        $total_packages = $document->items()->sum('quantity');
                    @endphp
                    <td ><strong>TOTAL NÚMERO DE BULTOS:</strong> 
                        @if(((int)$total_packages != $total_packages))
                            {{ $total_packages }}
                        @else
                            {{ number_format($total_packages, 0) }}
                        @endif
                    </td>
                </tr> 
            </table>
        </td>

        <td width="25%" class="pl-3">
            <table class="full-width">
                <tr>
                    <td ><strong>PESO TOTAL:</strong> KGM: {{$total_weight}}</td>
                </tr> 
            </table>
        </td> 
    </tr>
</table>


<table class="full-width border-box mt-10 mb-10"> 
    <tr>
        <td width="50%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td colspan="2"><strong>OBSERVACIONES:</td>
                </tr>
                <tr>
                    <td>{{ $document->observations }}</td>
                </tr> 
            </table>
        </td>
        <td width="3%"></td>

        <td width="47%" class="">
            <table class="full-width">
                <tr>
                    <td rowspan="2"><strong>Representación impresa de la Guía de Remisión</strong></td>
                </tr> 
            </table>
        </td>

    </tr>
</table>

</body>
</html>
