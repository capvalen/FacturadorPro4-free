@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    $document_base = ($document->note) ? $document->note : null;
    $optional = $document->optional;

    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::all();

    if($document_base) {
        $affected_document_number = $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT);
    } else {
        $affected_document_number = null;
    }
    $bank_accounts = \App\Models\Tenant\BankAccount::all();
@endphp
<html>
<head>
    {{-- <title>{{ $document_number }}</title> --}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<table class="full-width">
    <tr>
        <td width="65%">
            @if($company->logo)
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo">
                </div>
            @endif
        </td>
        <td width="35%" class="border-box p-box-info text-center">
            <h5>{{ 'RUC '.$company->number }}</h5>
            <h5 class="text-center">{{ $document->document_type->description }}</h5>
            <h4 class="text-center font-bold">{{ $document_number }}</h4>
        </td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td class="pl-3">
            <div class="text-left">
                <p class="font-bold text-upp">{{ $company->name }}</p>
                <p style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address : '' }}
                    {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                    {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                    {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                </p>
                <p>{{ ($establishment->email !== '-')? $establishment->email : '' }}</h6>
                <p>{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</h6>
            </div>
        </td>
    </tr>
</table>
<table class="full-width pt-10">
    <tr>
        <td><p class="font-bold text-upp">Adquiriente</p></td>
        <td></td>
    </tr>
    <tr>
        <td width="65%">
            <table class="full-width">
                <tr>
                    <td>{{ $customer->identity_document_type->description }}:{{$customer->number}}</td>
                </tr>
                <tr>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td>
                        @if ($customer->address !== '')
                            {{ $customer->address }}
                            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table class="full-width">
                <tr>
                    <td>FECHA DE EMISIÓN:</td>
                    <td>{{$document->date_of_issue->format('Y-m-d')}}</td>
                </tr>
                @if($invoice)
                    <tr>
                        <td>FECHA DE VENCIMIENTO:</td>
                        <td>{{$invoice->date_of_due->format('Y-m-d')}}</td>
                    </tr>
                @endif
                @if ($document->purchase_order)
                    <tr>
                        <td>ORDEN DE COMPRA:</td>
                        <td>{{ $document->purchase_order }}</td>
                    </tr>
                @endif
                @if ($document->guides)
                    @foreach($document->guides as $guide)
                        <tr>
                            @if(isset($guide->document_type_description))
                                <td>{{ $guide->document_type_description }}</td>
                            @else
                                <td>{{ $guide->document_type_id }}</td>
                            @endif
                            {{-- <td>{{ \App\Models\Tenant\Catalogs\Code::byCatalogAndCode('01', $guide->document_type_code)->description }}</td>
                            <td>{{ $guide->number }}</td> --}}
                        </tr>
                    @endforeach
                @endif
            </table>
        </td>
    </tr>
</table>

<table class="full-width mt-3">
    @if ($document->quotation_id)
        <tr>
            <td>COTIZACIÓN</td>
            <td>:</td>
            <td>{{ $document->quotation->identifier }}</td>
        </tr>
    @endif
    @if(!is_null($document_base))
    <tr>
        <td width="120px">DOC. AFECTADO</td>
        <td width="8px">:</td>
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
    @endif
</table>

<table class="mt-10 mb-10" style="border-collapse: collapse;border-top: 1px solid #333;">
    <tr class="bg-grey">
        <th class="text-left py-2" width="22">COD.</th>
        <th class="text-center py-2" width="5%">CANT.</th>
        <th class="text-center py-2" width="5%">UM</th>
        <th class="text-left py-2">DESCRIPCIÓN</th>
        <th class="text-right py-2" width="10%">P.UNIT</th>
        <th class="text-right py-2" width="8%">DTO.</th>
        <th class="text-right py-2" width="10%">TOTAL</th>
    </tr>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td >
                {{ $row->item->internal_id }}
            </td>
            <td class="text-center align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center align-top">
                {{ $row->item->unit_type_id }}
            </td>
            <td class="text-left align-top">
                {!!$row->item->description!!}
                @if (!empty($row->item->presentation))
                    {!!$row->item->presentation->description!!}
                @endif
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 7px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 7px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif
            </td>
            <td class="text-right align-top">
                {{ number_format($row->unit_price, 2) }}
            </td>
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
            <td class="text-right align-top">
                {{ number_format($row->total, 2) }}
            </td>
        </tr>
        <tr>
            <td colspan="7" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="6" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
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
            <td colspan="6" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width">
    <tr>
        <td width="65%" style="text-align: top; vertical-align: top;">
            @foreach(array_reverse( (array) $document->legends) as $row)
                @if ($row->code == "1000")
                    <p style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
                    @if (count((array) $document->legends)>1)
                        <p><span class="font-bold">Leyendas</span></p>
                    @endif
                @else
                    <p> {{$row->code}}: {{ $row->value }} </p>
                @endif

            @endforeach
            <br/>
            @foreach($document->additional_information as $information)
                @if ($information)
                    @if ($loop->first)
                        <p class="font-bold">Información adicional</p>
                    @endif
                    <p>{{ $information }}</p>
                @endif
            @endforeach
            <br>
            @if(in_array($document->document_type->id,['01','03']))
                <p class="font-bold">Cuentas bancarias</p>
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
        </td>
        <td width="35%" class="text-right">
            <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;" />
            <p style="font-size: 9px">Código Hash: {{ $document->hash }}</p>
        </td>
    </tr>
</table>
</body>
</html>
