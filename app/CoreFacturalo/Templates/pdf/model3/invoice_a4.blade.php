@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$template = 'default';
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::all();
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
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        @else
            <td width="20%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h4 class="">{{ $company->name }}</h4>
                <h5>{{ 'RUC '.$company->number }}</h5>
                <h6 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </h6>

                @isset($establishment->trade_address)
                    <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
                @endisset

                <h6>{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</h6>

                <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

                @isset($establishment->web_address)
                    <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
                @endisset

                @isset($establishment->aditional_information)
                    <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
                @endisset
            </div>
        </td>
        <td width="30%" class="border-box py-4 px-2 text-center">
            <h5 class="text-center">{{ $document->document_type->description }}</h5>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    {{--<tr><td colspan="3" style="height: 50px"></td></tr>--}}
    <tr>
        <td width="120px">FECHA DE EMISION</td>
        <td width="10px">:</td>
        <td>{{ $document->date_of_issue->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td>SEÑOR(es)</td>
        <td>:</td>
        <td>{{ $customer->name }}</td>
    </tr>
    <tr>
        <td>{{ $customer->identity_document_type->description }}:</td>
        <td>:</td>
        <td>{{ $customer->number }}</td>
    </tr>
    @if ($customer->address && $customer->address !== '' && $customer->address !== '-')
    <tr>
        <td>DIRECCIÓN</td>
        <td>:</td>
        <td>{{ $customer->address }}, {{ strtoupper($customer->department->description) }}-{{ strtoupper($customer->province->description) }}-{{ strtoupper($customer->district->description) }}
        </td>
        {{--<td>DIRECCIÓN</td>--}}
        {{--<td>:</td>--}}
        {{--<td>{{ $customer->address }}</td>--}}
    </tr>
    @endif
    @if ($customer->telephone)
    <tr>
        <td>TELÉFONO</td>
        <td>:</td>
        <td>{{ $customer->telephone }}</td>
    </tr>
    @endif
    <tr>
        <td>MONEDA</td>
        <td>:</td>
        <td>{{ $document->currency_type->description }}</td>
    </tr>
    @if ($document->payment_method_type_id)
    <tr>
        <td>FORMA DE PAGO</td>
        <td>:</td>
        <td>{{ $document->payment_method_type->description }}</td>
    </tr>
    @endif
</table>

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

<table class="full-width mt-10 mb-10 border-box table-items">
    <thead class="">
    <tr class="">
        <th class="border-bottom text-center py-2 w-60">CANT.</th>
        <th class="border-bottom text-center py-2 w-60">UND.</th>
        <th class="border-bottom text-center py-2">DESCRIPCIÓN</th>
    <th class="border-bottom text-right py-2">P.UNIT</th>
    <th class="border-bottom text-right py-2">DTO.</th>
        <th class="border-bottom text-right py-2">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-left">
                {{ $row->quantity }}
            </td>
             <td class="text-center align-top">
        @php
                $unit_type_description = \App\Models\Tenant\Catalogs\UnitType::find($row->item->unit_type_id);
                @endphp
                {{ $unit_type_description->description }}
            </td>
            <td class="text-left">
                {!! $row->item->description !!}
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
        {{--<tr>--}}
            {{--<td colspan="2" class="border-bottom"></td>--}}
        {{--</tr>--}}
    @endforeach
    <tr>
        <td colspan="4" style="height: 10px;"></td>
    </tr>
    <tr>
        <td colspan="4">
            @foreach($document->legends as $row)
                <p style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
            @endforeach
            <br/>
            @if($document->additional_information[0] != '')
                <strong>Información adicional</strong>
                @foreach($document->additional_information as $information)
                    <p>{{ $information }}</p>
                @endforeach
            @endif
        </td>
    </tr>
    </tbody>
</table>

<table class="full-width">
    <tr>
        <td width="65%">
            @if(in_array($document->document_type->id,['01','03']))
                @foreach($accounts as $account)
                    <p>
                    <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                    <span class="font-bold">N°:</span> {{$account->number}}
                    @if($account->cci)
                    <span class="font-bold">CCI:</span> {{$account->cci}}
                    @endif
                    </p>
                @endforeach
            @endif
            <div class="text-left"><img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" /></div>
            <p>Código Hash: {{ $document->hash }}</p>
            <p class="text-center desc pt-5">Para consultar el comprobante ingresar a {!! url('/buscar') !!}</p>
        </td>
        <td width="35%" class="align-top">
            <table class="full-width">
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
            </table>
        </td>
    </tr>
</table>
</body>
</html>
