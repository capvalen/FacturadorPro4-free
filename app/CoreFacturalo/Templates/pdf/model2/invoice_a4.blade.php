@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
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
            <td width="175px">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        {{--@else--}}
            {{--<td width="20%">--}}
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            {{--</td>--}}
        @endif
        <td width="100%" class="pl-3">
            <div class="text-left">
                <h2 class="">{{ $company->name }}</h2>
                <h4>{{ 'RUC '.$company->number }}</h4>
                @if($establishment->address !== '' && $establishment->address !== '-')
                <h4>
                    {{ $establishment->address }}<br/>
                    {{ strtoupper($establishment->department->description) }}-{{ strtoupper($establishment->province->description) }}-{{ strtoupper($establishment->district->description) }}
                </h4>
                @endif
                @if($establishment->email !== '' && $establishment->email !== '-')
                <h4>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h4>
                @endif
                @if($establishment->telephone !== '' && $establishment->telephone !== '-')
                <h4>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h4>
                @endif
            </div>
        </td>
        <td width="30%" class="border-box x-2 text-center" style="padding: 20px">
            <h5 class="text-center" style="font-size: 16px">{{ $document->document_type->description }}</h5>
            <br/>
            <h3 class="text-center" style="font-size: 26px">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<br/>
<table class="full-width">
    <tr>
        <td>
            <table class="full-width">
                <tr>
                    <td>Cliente</td>
                    <td>:</td>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td>{{ $customer->identity_document_type->description }}</td>
                    <td>:</td>
                    <td>{{ $customer->number }}</td>
                </tr>
                @if ($customer->address && $customer->address !== '' && $customer->address !== '-')
                <tr>
                    <td>Dirección</td>
                    <td>:</td>
                    <td>{{ $customer->address }}<br/>
                        {{ strtoupper($customer->department->description) }}-{{ strtoupper($customer->province->description) }}-{{ strtoupper($customer->district->description) }}
                    </td>
                </tr>
                @endif
                @if ($document->purchase_order)
                <tr>
                    <td>Orden de Compra</td>
                    <td>:</td>
                    <td>{{ $document->purchase_order }}</td>
                </tr>
                @endif
                @if ($document->guides)
                @foreach($document->guides as $guide)
                <tr>
                    <td>{{ $guide->document_type_id }}</td>
                    <td>:</td>
                    <td>{{ $guide->number }}</td>
                </tr>
                @endforeach
                @endif
            </table>
        </td>
        <td>
            <table class="full-width">
                <tr>
                    <td class="width-first-td">F. Emisión</td>
                    <td>:</td>
                    <td>{{ $document->date_of_issue->format('Y-m-d') }}</td>
                </tr>
                @isset($invoice->date_of_due)
                @if($document->date_of_issue->format('Y-m-d') !== $invoice->date_of_due->format('Y-m-d'))
                <tr>
                    <td>F. Vencimiento:</td>
                    <td>:</td>
                    <td>{{ $invoice->date_of_due->format('Y-m-d') }}</td>
                </tr>
                @endif
                @endisset
            </table>
        </td>
    </tr>
</table>

{{--<table class="full-width mt-5">--}}
    {{--<tr>--}}
        {{--<td width="15%">Cliente:</td>--}}
        {{--<td width="45%">{{ $customer->name }}</td>--}}
        {{--<td width="25%">Fecha de emisión:</td>--}}
        {{--<td width="15%">{{ $document->date_of_issue->format('Y-m-d') }}</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<td>{{ $customer->identity_document_type->description }}:</td>--}}
        {{--<td>{{ $customer->number }}</td>--}}
        {{--@if($invoice)--}}
        {{--<td>Fecha de vencimiento:</td>--}}
        {{--<td>{{ $invoice->date_of_due->format('Y-m-d') }}</td>--}}
        {{--@endif--}}
    {{--</tr>--}}
    {{--@if ($customer->address && $customer->address !== '' && $customer->address !== '-')--}}
    {{--<tr>--}}
        {{--<td>Dirección</td>--}}
        {{--<td>{{ $customer->address }}<br/>--}}
            {{--{{ strtoupper($customer->department->description) }}-{{ strtoupper($customer->province->description) }}-{{ strtoupper($customer->district->description) }}--}}
        {{--</td>--}}
    {{--</tr>--}}
    {{--@endif--}}
{{--</table>--}}

<table class="full-width mt-3">
    @if ($document->purchase_order)
        <tr>
            <td width="25%">Orden de Compra: </td>
            <td class="text-left">{{ $document->purchase_order }}</td>
        </tr>
    @endif
    @if ($document->guides)
        @foreach($document->guides as $guide)
            <tr>
                <td>{{ $guide->document_type_id }}</td>
                <td>{{ $guide->number }}</td>
            </tr>
        @endforeach
    @endif
</table>

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2">CANT.</th>
        <th class="border-top-bottom text-center py-2">UND.</th>
        <th class="border-top-bottom text-left py-2">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-2">P.UNIT</th>
        <th class="border-top-bottom text-right py-2">DSCTO.</th>
        <th class="border-top-bottom text-right py-2">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center align-top">{{ $row->quantity }}</td>
            <td class="text-center align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left">
                {!! $row->item->description !!}
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->description !!} : {{ $attr->value }}
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><small>{{ $dtos->factor * 100 }}% {{$dtos->description }}</small>
                    @endforeach
                @endif
            </td>
            <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
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
            <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
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
                <td colspan="5" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">ICBPER: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
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
</table>
<table class="full-width">
    <tr>
        <td width="65%">
            @foreach($document->legends as $row)
                <p style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
            @endforeach
            <br/>
            @if($document->additional_information)
            <strong>Información adicional</strong>
            @foreach($document->additional_information as $information)
            <p>{{ $information }}</p>
            @endforeach
            @endif
            <div class="text-left"><img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" /></div>
            <p>Código Hash: {{ $document->hash }}</p>
        </td>
    </tr>
</table>
</body>
</html>
