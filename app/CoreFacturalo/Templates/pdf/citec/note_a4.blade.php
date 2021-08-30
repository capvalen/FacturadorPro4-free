@php
    $establishment = $document->establishment;
    $customer = $document->customer;

    $document_base = $document->note;
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $document_type_description_array = [
        '01' => 'FACTURA',
        '03' => 'BOLETA DE VENTA',
        '07' => 'NOTA DE CREDITO',
        '08' => 'NOTA DE DEBITO',
    ];
    $identity_document_type_description_array = [
        '-' => 'S/D',
        '0' => 'S/D',
        '1' => 'DNI',
        '6' => 'RUC',
    ];
    $affected_document_number = $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT);
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>

<table class="full-width">
    <tr>
        <td width="50%" class="pl-3">

            @if($company->logo)

                    <div class="company_logo_box">
                        <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                    </div>
            @else

                    {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}

            @endif

            <div class="text-left">
                <h4 class="">Domicilio Fiscal: Jr. Diego de Almagro N 256 - Int. 101 <br> Centro Trujillo - La Libertad - Trujillo Trujillo <br> Telf: 044-693614 - 948836100 </h4>
                <h4>Sucursal: {{ $establishment->address}} <br> 076-283327 - 9769824444  </h4>
                {{-- <h6>
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h6>
                <h6>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h6>
                <h6>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h6> --}}
            </div>
        </td>
        <td width="50%" class="border-box py-4 px-2 text-center" style="background-color: #5DA8EB;" >

            <h5 style="color:white;font-weight:bold" class="text-center">R.U.C. {{$company->number }}</h5>
            <h5  style="color:white;font-weight:bold" class="text-center">{{ $document->document_type->description }}</h5>
            <h5  style="color:white;font-weight:bold" class="text-center">{{ $document_number }}</h5>

        </td>
    </tr>

</table>
<br>

<table class="full-width">

    <tr>

        <td width="100%" class="border-box text-center"> <span class="" style="color:#0483F3"> Ventas de equipo de computo, equipos de  impresion, Ingenieria, Comunicaciones, Redes, Seguridad <br>
        Partes, Accesorios, suministros, Originales y compatibles - Servicios Informaticos </span></td>
        </td>
    </tr>

</table>

<table class="full-width mt-5">
    <tr>
        <td width="120px">FECHA DE EMISIÓN</td>
        <td width="5px">:</td>
        <td>{{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>CLIENTE</td>
        <td>:</td>
        <td>{{ $customer->name }}</td>
    </tr>
    <tr>
        <td>{{ $customer->identity_document_type->description }}</td>
        <td>:</td>
        <td>{{ $customer->number }}</td>
        {{--@isset($document->date_of_due)--}}
            {{--<td>Fecha de vencimiento:</td>--}}
            {{--<td>{{ $document->date_of_due->format('Y-m-d') }}</td>--}}
        {{--@endisset--}}
    </tr>
    @if ($customer->address !== '')
    <tr>
        <td class="align-top">DIRECCIÓN</td>
        <td>:</td>
        <td>
            {{ $customer->address }}
            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
        </td>
    </tr>
    @endif
</table>

@if ($document->guides)
<table class="full-width mt-3">
@foreach($document->guides as $guide)
    <tr>
        <td>{{ $guide->document_type_id }}</td>
        <td>{{ $guide->number }}</td>
    </tr>
@endforeach
</table>
@endif

<table class="full-width mt-3">
    @if ($document->purchase_order)
    <tr>
        <td>ORDEN DE COMPRA</td>
        <td>:</td>
        <td>{{ $document->purchase_order }}</td>
    </tr>
    @endif
    <tr>
        <td width="120px">DOC. AFECTADO</td>
        <td width="5px">:</td>
        <td>{{ $affected_document_number }}</td>
    </tr>
    <tr>
        <td>TIPO DE NOTA</td>
        <td>:</td>
        <td>{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
    </tr>
    <tr>
        <td>DESCRIPCIÓN</td>
        <td>:</td>
        <td>{{ $document_base->note_description }}</td>
    </tr>
</table>
<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2" width="8%">UNIDAD</th>
        <th class="border-top-bottom text-left py-2">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-2" width="12%">V.UNIT</th>
        <th class="border-top-bottom text-right py-2" width="8%">DTO.</th>
        <th class="border-top-bottom text-right py-2" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center">{{ $row->item->unit_type_id }}</td>
            <td class="text-left">
                {!! $row->item->description !!}
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif
            </td>
            <td class="text-right">{{ number_format($row->unit_value, 4) }}</td>
            <td class="text-right align-top">
                @if($row->discounts)
                    @php
                        $total_discount_line = 0;
                        foreach ($row->discounts as $disto) {
                            $total_discount_line = $total_discount_line + $disto->amount;
                        }
                    @endphp
                    {{ number_format($total_discount_line, 2) }}
                @else
                0
                @endif
            </td>
            <td class="text-right">{{ number_format($row->total_value, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
        @if($document->total_discount > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">DESCUENTO TOTAL: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
    <tfoot style="border-top: 1px solid #333;">
    <tr>
        <td colspan="5" class="font-lg"  style="padding-top: 2rem;">Son: <span class="font-bold">{{ $document->number_to_letter }} {{ $document->currency_type->description }}</span></td>
    </tr>
    @if(isset($document->optional->observations))
        <tr>
            <td colspan="3"><b>Obsevaciones</b></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3">{{ $document->optional->observations }}</td>
            <td colspan="2"></td>
        </tr>
    @endif
    </tfoot>
</table>

<table class="full-width">
    <tr>
        <td width="65%">
            <div class="text-left"><img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" /></div>
            <p>Código Hash: {{ $document->hash }}</p>
        </td>
    </tr>
</table>
</body>
</html>
