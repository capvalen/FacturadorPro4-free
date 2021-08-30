@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    $document_base = ($document->note) ? $document->note : null;

    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::all();

    if($document_base) {
        $affected_document_number = $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT);
    } else {
        $affected_document_number = null;
    }

    $config = \App\Models\Tenant\Configuration::first();
    $miimage = null;
    if($config->formats == "wsc")
    {
        $miimage = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'wsc'.DIRECTORY_SEPARATOR.'membrete.jpg');
    }


@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<div class="" style="position: absolute; text-align: center; z-index: 0; width:45%; top: 50px;">
    <img src="data:{{mime_content_type($miimage)}};base64, {{base64_encode(file_get_contents($miimage))}}" alt="anulado" class="">
</div>
<div style="z-index: 1; position: absolute;width: 280px; left: 410px;">
        <div class="pb-4 pt-2 px-3 text-center" style="background-color: rgb(160,11,188);">
                <h3 style="color:white;font-weight:bold" class="text-center">R.U.C. {{$company->number }}</h3>
                <h3  style="color:white;font-weight:bold" class="text-center">{{ $document->document_type->description }}</h3>
                <h3  style="color:white;font-weight:bold" class="text-center">{{ $document_number }}</h3>
        </div>
</div>
<div  style="position: fixed; top:150px;">

    <table class="full-width mt-5">
        <tr>
            <td width="15%" class="font-bold desc align-top">SEÑOR(ES):</td>
            <td width="55%" class="desc">{{ $customer->name }}</td>
            <td width="15%" class="font-bold desc align-top">FECHA:</td>
            <td width="15%" class="desc">{{ $document->date_of_issue->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="font-bold desc">{{ $customer->identity_document_type->description }}:</td>
            <td class="desc">{{ $customer->number }}</td>
            <td class="font-bold desc">MONEDA:</td>
            <td class="desc">{{ $document->currency_type->description }}</td>
        </tr>
        @if ($customer->address !== '')
        <tr>
            <td class="align-top desc font-bold">DIRECCIÓN:</td>
            <td class="desc">
                {{ $customer->address }}
                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
            </td>
            @if ($document->guides)
                <td class="font-bold align-top desc">GUIA:</td>
                <td class="desc">
                    @foreach($document->guides as $guide)
                        {{ $guide->number }}<br>
                    @endforeach
                </td>
            @endif
        </tr>
        @endif
    </table>

    <table class="full-width mt-3">
        @if ($document->purchase_order)
            <tr>
                <td class="font-bold desc">ORDEN DE COMPRA</td>
                <td>:</td>
                <td class="desc">{{ $document->purchase_order }}</td>
            </tr>
        @endif
        @if ($document->quotation_id)
            <tr>
                <td class="font-bold desc">COTIZACIÓN</td>
                <td>:</td>
                <td class="desc">{{ $document->quotation->identifier }}</td>
            </tr>
        @endif
        @if(!is_null($document_base))
        <tr>
            <td width="120px" class="font-bold desc">DOC. AFECTADO</td>
            <td width="8px">:</td>
            <td class="desc">{{ $affected_document_number }}</td>
        </tr>
        <tr>
            <td class="font-bold desc">TIPO DE NOTA</td>
            <td>:</td>
            <td class="desc">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
        </tr>
        <tr>
            <td class="font-bold desc">DESCRIPCIÓN</td>
            <td>:</td>
            <td class="desc">{{ $document_base->note_description }}</td>
        </tr>
        @endif
    </table>

    {{--<table class="full-width mt-3">--}}
        {{--<tr>--}}
            {{--<td width="25%">Documento Afectado:</td>--}}
            {{--<td width="20%">{{ $document_base->affected_document->series }}-{{ $document_base->affected_document->number }}</td>--}}
            {{--<td width="15%">Tipo de nota:</td>--}}
            {{--<td width="40%">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td class="align-top">Descripción:</td>--}}
            {{--<td class="text-left" colspan="3">{{ $document_base->note_description }}</td>--}}
        {{--</tr>--}}
    {{--</table>--}}

    <table class="full-width mt-10 mb-10">
        <thead class="">
        <tr class="" style="background-color: rgb(160,11,188);">
            <th class="border-top-bottom text-left text-white">CODIGO</th>
            <th class="border-top-bottom text-left text-white">DESCRIPCIÓN</th>
            <th class="border-top-bottom text-center text-white" width="8%">UNID.</th>
            <th class="border-top-bottom text-center text-white" width="10%">CANTIDAD</th>
            <th class="border-top-bottom text-right text-white" width="12%">V.UNIT</th>
            <th class="border-top-bottom text-right text-white" width="12%">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($document->items as $row)
            <tr>
                <td class="text-left align-top">{{ $row->item->internal_id }}</td>
                <td class="text-left align-top">
                    {!!$row->item->description!!} @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
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
                <td class="text-center align-top">{{ $row->item->unit_type_id }}</td>
                <td class="text-center align-top">
                    @if(((int)$row->quantity != $row->quantity))
                        {{ $row->quantity }}
                    @else
                        {{ number_format($row->quantity, 0) }}
                    @endif
                </td>
                <td class="text-right align-top">{{ number_format($row->unit_value, 4) }}</td>
                <td class="text-right align-top">{{ number_format($row->total_value, 2) }}</td>
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
            <td width="65%" style="text-align: top; vertical-align: top;">
                @foreach(array_reverse( (array) $document->legends) as $row)
                    @if ($row->code == "1000")
                        <p>Son: <span class="">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
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
                            <strong>Información adicional</strong>
                        @endif
                        <p>{{ $information }}</p>
                    @endif
                @endforeach
                <br>
                @if(in_array($document->document_type->id,['01','03']))
                    @foreach($accounts as $account)
                        <p><span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}} {{$account->number}}</p>
                    @endforeach
                @endif
            </td>
        </tr>
    </table>
    <div class="text-center desc-9">
        <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;" />
        <p class="desc-9">Representación impresa de <br> <span class="font-bold">{{ $document->document_type->description }}</span> <br> Consulte su comprobante de pago en: <br> <span class="font-bold">{!! url('/buscar') !!} </span></p>
        <p class="desc-9">Código Hash: {{ $document->hash }}</p>
    </div>

</div>
</body>
</html>
