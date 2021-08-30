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


@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
    <div class="header">
        <div class="text-center float-left header-logo">
            @if($company->logo)
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            @endif
        </div>
        <div class="text-center float-left header-company">
                <span class="font-xlg text-uppercase font-bold">{{ $company->name }}</span>
                <br>
                {{ ($establishment->address !== '-')? $establishment->address.',' : '' }}
                {{ ($establishment->district_id !== '-')? ' '.$establishment->district->description : '' }}
                {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                <br>
                {{ ($establishment->email !== '-')? $establishment->email : '' }}
                <br>
                {{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}
                <br>
        </div>
        <div  class="text-center float-left header-number py-3 font-bold">
            RUC
            <br>
            {{$company->number }}
            <br>
            {{ $document->document_type->description }}
            <br>
            {{ $document_number }}
        </div>
    </div>

    <div class="information mt-3">
        <div class="div-table">
             <div class="div-table-row">
                <div class="div-table-col w-10">Emisión</div>
                <div  class="div-table-col w-60">{{$document->date_of_issue->format('Y-m-d')}}</div>
                <div class="div-table-col w-15">Guía Nro.</div>
                <div  class="div-table-col w-10">
                    @if ($document->guides)
                    <table>
                        @foreach($document->guides as $guide)
                            <tr>
                                @if(isset($guide->document_type_description))
                                <td>{{ $guide->document_type_description }}</td>
                                @else
                                <td>{{ $guide->document_type_id }}</td>
                                @endif
                                <td>:</td>
                                <td>{{ $guide->number }}</td>
                            </tr>
                        @endforeach
                    </table>
                    @endif
                    @if ($document->reference_guides)
                    <table>
                        @foreach($document->reference_guides as $guide)
                            <tr>
                                <td>{{ $guide->series }}</td>
                                <td>-</td>
                                <td>{{ $guide->number }}</td>
                            </tr>
                        @endforeach
                    </table>
                    @endif
                </div>
             </div>
            <div class="div-table-row">
                <div class="div-table-col w-10">Cliente</div>
                <div class="div-table-col w-60">{{ $customer->name }}</div>
                <div class="div-table-col w-15">O/C</div>
                <div  class="div-table-col w-10">
                    @if ($document->purchase_order)
                        {{ $document->purchase_order }}
                    @endif
                </div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col w-10">{{ $customer->identity_document_type->description }}</div>
                <div class="div-table-col w-60">{{$customer->number}}</div>
                <div class="div-table-col w-15">Vencimiento</div>
                <div  class="div-table-col w-10">
                    @if($invoice)
                        {{$invoice->date_of_due->format('Y-m-d')}}
                    @endif
                </div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col w-10">Dirección</div>
                <div class="div-table-col w-60">
                    {{ $customer->address }}
                    {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                    {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                    {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                </div>
                <div class="div-table-col w-15">Cond. Pago</div>
                <div  class="div-table-col w-10">
                    @if ($document->payment_condition_id === '01')
                        @if($document->payments->count())
                            {{$document->payments[0]->payment_method_type->description}}
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>

    <div class="font-bold pt-2 desc">Observación</div>
    <div class="information">
        <div class="div-table">
            <div class="div-table-row">
                <div class="div-table-col w-10 font-bold">CANTIDAD</div>
                <div class="div-table-col w-60 font-bold text-center">DESCRIPCIÓN</div>
                <div class="div-table-col w-19 font-bold text-right">PRECIO UNIT.</div>
                <div class="div-table-col w-10 font-bold text-right">IMPORTE</div>
            </div>
            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
            @foreach($document->items as $row)
                <div class="div-table-row">
                    <div class="div-table-col w-10 text-right pr-2">
                        @if(((int)$row->quantity != $row->quantity))
                            {{ $row->quantity }}
                        @else
                            {{ number_format($row->quantity, 0) }}
                        @endif
                    </div>
                    <div class="div-table-col w-60">
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
                        @if($row->item->is_set == 1)
                        <br>
                        @inject('itemSet', 'App\Services\ItemSetService')
                        @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                            {{$item}}<br>
                        @endforeach
                        @endif
                    </div>
                    <div class="div-table-col w-19 text-right">{{ number_format($row->unit_price, 4) }}</div>
                    <div class="div-table-col w-10 text-right">{{ number_format($row->total, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="div-table">
        <div class="div-table-row">
            <div class="div-table-col w-60">
                @foreach(array_reverse( (array) $document->legends) as $row)
                    @if ($row->code == "1000")
                        SON <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span>
                    @endif
                @endforeach
                <br>
                <img src="data:image/png;base64, {{ $document->qr }}" />
                <br>
                <span style="font-size: 9px">Código Hash: {{ $document->hash }}</span>
                <br>
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
            </div>
            <div class="div-table-col w-40">
                <div class="information">
                    <div class="div-table">
                        @if($document->total_exportation > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_exportation, 2) }}</div>
                            </tr>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_free > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_free, 2) }}</div>
                            </div>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_unaffected > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_unaffected, 2) }}</div>
                            </div>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_exonerated > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_exonerated, 2) }}</div>
                            </div>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_discount > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">DESCUENTO TOTAL: {{ $document->currency_type->symbol }}</div>
                              div<div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_discount, 2) }}</div>
                            </tr>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_plastic_bag_taxes > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">ICBPER: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_plastic_bag_taxes, 2) }}<divd>
                            </div>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        @if($document->total_taxed > 0)
                            <div class="div-table-row">
                                <div class="div-table-col w-50 font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</div>
                                <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_taxed, 2) }}</div>
                            </div>
                            <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        @endif
                        <div class="div-table-row pt-1">
                            <div class="div-table-col w-50 font-bold">IGV: {{ $document->currency_type->symbol }}</div>
                            <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total_igv, 2) }}</div>
                        </div>
                        <div class="border-bottom pt-1" style="margin-right: -5px;margin-left: -5px;"></div>
                        <div class="div-table-row pt-1">
                            <div class="div-table-col w-50 font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</div>
                            <div class="div-table-col w-50 font-bold text-right">{{ number_format($document->total, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



{{-- <table class="full-width mt-3">
    @if ($document->purchase_order)
        <tr>
            <td>ORDEN DE COMPRA</td>
            <td>:</td>
            <td>{{ $document->purchase_order }}</td>
        </tr>
    @endif
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
</table> --}}

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

{{-- <table class="full-width">
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
                        <strong>Información adicional</strong>
                    @endif
                    <p>{{ $information }}</p>
                @endif
            @endforeach
            <br>
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
        </td>
        <td width="35%" class="text-right">
            <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;" />
            <p style="font-size: 9px">Código Hash: {{ $document->hash }}</p>
        </td>
    </tr>
</table> --}}
</body>
</html>
