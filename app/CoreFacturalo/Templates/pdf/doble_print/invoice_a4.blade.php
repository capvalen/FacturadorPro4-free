@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    $document_base = ($document->note) ? $document->note : null;

    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $accounts = \App\Models\Tenant\BankAccount::all();

    if($document_base) {

        $affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

    } else {

        $affected_document_number = null;
    }

    $payments = $document->payments;

    $document->load('reference_guides');

    $total_payment = $document->payments->sum('payment');
    $balance = ($document->total - $total_payment) - $document->payments->sum('change');

@endphp
<html>
<head>
</head>
<body>

@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:20%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="20%">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        @else
            <td width="20%"></td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h4 class="">{{ $company->name }}</h4>
                <h5>{{ 'RUC '.$company->number }}</h5>
                <h6 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address.', ' : '' }}
                    {{ ($establishment->district_id !== '-')? $establishment->district->description : '' }}
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
<table class="full-width">
    <tr>
        <td width="90%" class="align-top">
            <table class="full-width mt-2">
                <tr>
                    <td colspan="3">
                        <table class="full-width">
                            <tr>
                                <td width="120px">FECHA DE EMISIÓN</td>
                                <td width="8px">:</td>
                                <td width="220px">{{$document->date_of_issue->format('Y-m-d')}}</td>
                                @if($invoice)
                                    <td width="130px">FECHA DE VENCIMIENTO</td>
                                    <td width="8px">:</td>
                                    <td>{{$invoice->date_of_due->format('Y-m-d')}}</td>
                                @else
                                    <td colspan="3"></td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="120px">CLIENTE</td>
                    <td width="8px">:</td>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td>{{ $customer->identity_document_type->description }}</td>
                    <td>:</td>
                    <td>{{$customer->number}}</td>
                </tr>
                @if ($customer->address !== '')
                <tr>
                    <td class="align-top">DIRECCIÓN</td>
                    <td class="align-top">:</td>
                    <td style="text-transform: uppercase;">
                        {{ $customer->address }}
                        {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                        {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                        {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                    </td>
                </tr>
                @endif
                @if ($document->prepayments)
                    @foreach($document->prepayments as $p)
                    <tr>
                        <td>ANTICIPO</td>
                        <td>:</td>
                        <td>{{$p->number}}</td>
                    </tr>
                    @endforeach
                @endif
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
                    @isset($document->quotation->delivery_date)
                        <tr>
                            <td>F. ENTREGA</td>
                            <td>:</td>
                            <td>{{ $document->quotation->delivery_date->format('Y-m-d')}}</td>
                        </tr>
                    @endisset
                @endif
                @isset($document->quotation->sale_opportunity)
                    <tr>
                        <td>O. VENTA</td>
                        <td>:</td>
                        <td>{{ $document->quotation->sale_opportunity->number_full}}</td>
                    </tr>
                @endisset
                @if(!is_null($document_base))
                    <tr>
                        <td>DOC. AFECTADO</td>
                        <td>:</td>
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
                @if ($document->detraction)
                <tr>
                    <td colspan="3">
                        <table class="full-width" >
                            <tr>
                                <td width="120px">N. CTA DETRACCIONES</td>
                                <td width="8px">:</td>
                                <td width="220px">{{ $document->detraction->bank_account}}</td>
                                <td>B/S SUJETO A DETRACCIÓN</td>
                                <td width="8px">:</td>
                                <td>
                                    @inject('detractionType', 'App\Services\DetractionTypeService')
                                    {{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}
                                </td>
                            </tr>
                            <tr>
                                <td>MÉTODO DE PAGO</td>
                                <td>:</td>
                                <td>{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</td>
                                <td>P. DETRACCIÓN</td>
                                <td>:</td>
                                <td>{{ $document->detraction->percentage}}%</td>
                            </tr>
                            <tr>
                                <td>MONTO DETRACCIÓN</td>
                                <td>:</td>
                                <td>S/ {{ $document->detraction->amount}}</td>
                                @if($document->detraction->pay_constancy)
                                    <td>CONSTANCIA DE PAGO</td>
                                    <td>:</td>
                                    <td>{{ $document->detraction->pay_constancy}}</td>
                                @endif
                            </tr>
                        </table>
                    </td>
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
                            <td>:</td>
                            <td>{{ $guide->number }}</td>
                        </tr>
                    @endforeach
                @endif
                @if (count($document->reference_guides) > 0)
                    <tr>
                        <td>GUIAS DE REMISIÓN</td>
                        <td>:</td>
                        <td>
                            @foreach($document->reference_guides as $guide)
                                {{ $guide->series }}-{{ $guide->number }}
                            @endforeach
                        </td>
                    </tr>
                @endif
                @if($document->user)
                    <tr>
                        <td>VENDEDOR</td>
                        <td>:</td>
                        <td>{{ $document->user->name }}</td>
                    </tr>
                @endif
            </table>
        </td>
        <td class="align-top">
            <img src="data:image/png;base64, {{ $document->qr }}" style="height:100px;" />
        </td>
    </tr>
</table>
<table class="full-width">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2 desc" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">UNIDAD</th>
        <th class="border-top-bottom text-left py-2 desc">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">LOTE</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">SERIE</th>
        <th class="border-top-bottom text-right py-2 desc" width="12%">P.UNIT</th>
        <th class="border-top-bottom text-right py-2 desc" width="8%">DTO.</th>
        <th class="border-top-bottom text-right py-2 desc" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
        @foreach($document->items as $row)
        <tr>
            <td class="text-center align-top desc">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center align-top desc">{{ $row->item->unit_type_id }}</td>
            <td class="text-left align-top desc">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                {{-- @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif --}}

                @if($row->item->is_set == 1)
                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                    {{join( "-", $itemSet->getItemsSet($row->item_id) )}}
                @endif
            </td>
            <td class="text-center align-top desc">
                @inject('itemLotGroup', 'App\Services\ItemLotsGroupService')
                {{ $itemLotGroup->getLote($row->item->IdLoteSelected) }}

            </td>
            <td class="text-center align-top desc">

                @isset($row->item->lots)
                    @foreach($row->item->lots as $lot)
                        @if( isset($lot->has_sale) && $lot->has_sale)
                            <span style="font-size: 9px">{{ $lot->series }}</span><br>
                        @endif
                    @endforeach
                @endisset

            </td>
            <td class="text-right align-top desc">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right align-top desc">
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
            <td class="text-right align-top desc">{{ number_format($row->total, 2) }}</td>
        </tr>
        @endforeach
        @if(count($document->items) < 5)
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
        @endif
        <tr>
            <td colspan="8" class="border-bottom"></td>
        </tr>

        @if ($document->prepayments)
            @foreach($document->prepayments as $p)
            <tr>
                <td class="text-center align-top desc">
                    1
                </td>
                <td class="text-center align-top desc">NIU</td>
                <td class="text-left align-top desc">
                    ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
                </td>
                <td class="text-right align-top desc">-{{ number_format($p->total, 2) }}</td>
                <td class="text-right align-top desc">
                    0
                </td>
                <td class="text-right align-top desc">-{{ number_format($p->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="8" class="border-bottom"></td>
            </tr>
            @endforeach
        @endif

        <tr>
            <td colspan="4" class="align-top pt-2">
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
                @if ($document->detraction)
                <p>
                    <span class="font-bold">
                    Operación sujeta al Sistema de Pago de Obligaciones Tributarias
                    </span>
                </p>
                @endif
                @if ($customer->department_id == 16)
                    <div>
                        <center>
                            Representación impresa del Comprobante de Pago Electrónico.
                            <br/>Esta puede ser consultada en:
                            <br/><b>{!! url('/buscar') !!}</b>
                            <br/> "Bienes transferidos en la Amazonía
                            <br/>para ser consumidos en la misma".
                        </center>
                    </div>
                @endif
                @foreach($document->additional_information as $information)
                    @if ($information)
                        @if ($loop->first)
                            <strong>Información adicional</strong>
                        @endif
                        <p>{{ $information }}</p>
                    @endif
                @endforeach
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
                @if($payments->count())
                    <table class="full-width">
                        <tr>
                            <td>
                                <strong>PAGOS:</strong>
                            </td>
                        </tr>
                            @php
                                $payment = 0;
                            @endphp
                            @foreach($payments as $row)
                                <tr>
                                    <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
                                </tr>
                                @php
                                    $payment += (float) $row->payment;
                                @endphp
                            @endforeach
                        </tr>
                        <tr>
                            <td>
                                <strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
            <td colspan="4" width="30%" class="align-top">
                <table class="full-width">
                    @if($document->total_exportation > 0)
                        <tr>
                            <td class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_free > 0)
                        <tr>
                            <td class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_unaffected > 0)
                        <tr>
                            <td class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_exonerated > 0)
                        <tr>
                            <td class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_taxed > 0)
                        <tr>
                            <td class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_discount > 0)
                        <tr>
                            <td class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_plastic_bag_taxes > 0)
                        <tr>
                            <td class="text-right font-bold">ICBPER: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
                    </tr>

                    @if($document->perception)
                        <tr>
                            <td class="text-right font-bold"> IMPORTE TOTAL: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">PERCEPCIÓN: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
                        </tr>
                    @endif

                    @if($balance < 0)

                        <tr>
                            <td class="text-right font-bold">VUELTO: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
                        </tr>

                    @endif
                    @if($document->hash)
                        <tr>
                            <td class="pt-10 desc text-right" colspan="2">Código Hash: {{ $document->hash }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </tbody>
</table>

<br>

<!-- COPIA -->

@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:62%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="20%">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        @else
            <td width="20%"></td>
        @endif
        <td width="50%" class="pl-3">
            <div class="text-left">
                <h4 class="">{{ $company->name }}</h4>
                <h5>{{ 'RUC '.$company->number }}</h5>
                <h6 style="text-transform: uppercase;">
                    {{ ($establishment->address !== '-')? $establishment->address.', ' : '' }}
                    {{ ($establishment->district_id !== '-')? $establishment->district->description : '' }}
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
<table class="full-width">
    <tr>
        <td width="90%" class="align-top">
            <table class="full-width mt-2">
                <tr>
                    <td colspan="3">
                        <table class="full-width">
                            <tr>
                                <td width="120px">FECHA DE EMISIÓN</td>
                                <td width="8px">:</td>
                                <td width="220px">{{$document->date_of_issue->format('Y-m-d')}}</td>
                                @if($invoice)
                                    <td width="130px">FECHA DE VENCIMIENTO</td>
                                    <td width="8px">:</td>
                                    <td>{{$invoice->date_of_due->format('Y-m-d')}}</td>
                                @else
                                    <td colspan="3"></td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="120px">CLIENTE</td>
                    <td width="8px">:</td>
                    <td>{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td>{{ $customer->identity_document_type->description }}</td>
                    <td>:</td>
                    <td>{{$customer->number}}</td>
                </tr>
                @if ($customer->address !== '')
                <tr>
                    <td class="align-top">DIRECCIÓN</td>
                    <td class="align-top">:</td>
                    <td style="text-transform: uppercase;">
                        {{ $customer->address }}
                        {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                        {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                        {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                    </td>
                </tr>
                @endif
                @if ($document->prepayments)
                    @foreach($document->prepayments as $p)
                    <tr>
                        <td>ANTICIPO</td>
                        <td>:</td>
                        <td>{{$p->number}}</td>
                    </tr>
                    @endforeach
                @endif
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
                    @isset($document->quotation->delivery_date)
                        <tr>
                            <td>F. ENTREGA</td>
                            <td>:</td>
                            <td>{{ $document->quotation->delivery_date->format('Y-m-d')}}</td>
                        </tr>
                    @endisset
                @endif
                @isset($document->quotation->sale_opportunity)
                    <tr>
                        <td>O. VENTA</td>
                        <td>:</td>
                        <td>{{ $document->quotation->sale_opportunity->number_full}}</td>
                    </tr>
                @endisset
                @if(!is_null($document_base))
                    <tr>
                        <td>DOC. AFECTADO</td>
                        <td>:</td>
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
                @if ($document->detraction)
                <tr>
                    <td colspan="3">
                        <table class="full-width" >
                            <tr>
                                <td width="120px">N. CTA DETRACCIONES</td>
                                <td width="8px">:</td>
                                <td width="220px">{{ $document->detraction->bank_account}}</td>
                                <td>B/S SUJETO A DETRACCIÓN</td>
                                <td width="8px">:</td>
                                <td>
                                    @inject('detractionType', 'App\Services\DetractionTypeService')
                                    {{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}
                                </td>
                            </tr>
                            <tr>
                                <td>MÉTODO DE PAGO</td>
                                <td>:</td>
                                <td>{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</td>
                                <td>P. DETRACCIÓN</td>
                                <td>:</td>
                                <td>{{ $document->detraction->percentage}}%</td>
                            </tr>
                            <tr>
                                <td>MONTO DETRACCIÓN</td>
                                <td>:</td>
                                <td>S/ {{ $document->detraction->amount}}</td>
                                @if($document->detraction->pay_constancy)
                                    <td>CONSTANCIA DE PAGO</td>
                                    <td>:</td>
                                    <td>{{ $document->detraction->pay_constancy}}</td>
                                @endif
                            </tr>
                        </table>
                    </td>
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
                            <td>:</td>
                            <td>{{ $guide->number }}</td>
                        </tr>
                    @endforeach
                @endif
                @if (count($document->reference_guides) > 0)
                    <tr>
                        <td>GUIAS DE REMISIÓN</td>
                        <td>:</td>
                        <td>
                            @foreach($document->reference_guides as $guide)
                                {{ $guide->series }}-{{ $guide->number }}
                            @endforeach
                        </td>
                    </tr>
                @endif
                @if($document->user)
                    <tr>
                        <td>VENDEDOR</td>
                        <td>:</td>
                        <td>{{ $document->user->name }}</td>
                    </tr>
                @endif
            </table>
        </td>
        <td class="align-top">
            <img src="data:image/png;base64, {{ $document->qr }}" style="height:100px;" />
        </td>
    </tr>
</table>
<table class="full-width">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2 desc" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">UNIDAD</th>
        <th class="border-top-bottom text-left py-2 desc">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">LOTE</th>
        <th class="border-top-bottom text-center py-2 desc" width="8%">SERIE</th>
        <th class="border-top-bottom text-right py-2 desc" width="12%">P.UNIT</th>
        <th class="border-top-bottom text-right py-2 desc" width="8%">DTO.</th>
        <th class="border-top-bottom text-right py-2 desc" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
        @foreach($document->items as $row)
        <tr>
            <td class="text-center align-top desc">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center align-top desc">{{ $row->item->unit_type_id }}</td>
            <td class="text-left align-top desc">
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                {{-- @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif --}}

                @if($row->item->is_set == 1)
                 <br>
                 @inject('itemSet', 'App\Services\ItemSetService')
                    {{join( "-", $itemSet->getItemsSet($row->item_id) )}}
                @endif
            </td>
            <td class="text-center align-top desc">
                @inject('itemLotGroup', 'App\Services\ItemLotsGroupService')
                {{ $itemLotGroup->getLote($row->item->IdLoteSelected) }}

            </td>
            <td class="text-center align-top desc">

                @isset($row->item->lots)
                    @foreach($row->item->lots as $lot)
                        @if( isset($lot->has_sale) && $lot->has_sale)
                            <span style="font-size: 9px">{{ $lot->series }}</span><br>
                        @endif
                    @endforeach
                @endisset

            </td>
            <td class="text-right align-top desc">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right align-top desc">
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
            <td class="text-right align-top desc">{{ number_format($row->total, 2) }}</td>
        </tr>
        @endforeach
        @if(count($document->items) < 5)
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
            <tr><td colspan="8">&nbsp;</td></tr>
        @endif
        <tr>
            <td colspan="8" class="border-bottom"></td>
        </tr>

        @if ($document->prepayments)
            @foreach($document->prepayments as $p)
            <tr>
                <td class="text-center align-top desc">
                    1
                </td>
                <td class="text-center align-top desc">NIU</td>
                <td class="text-left align-top desc">
                    ANTICIPO: {{($p->document_type_id == '02')? 'FACTURA':'BOLETA'}} NRO. {{$p->number}}
                </td>
                <td class="text-right align-top desc">-{{ number_format($p->total, 2) }}</td>
                <td class="text-right align-top desc">
                    0
                </td>
                <td class="text-right align-top desc">-{{ number_format($p->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="8" class="border-bottom"></td>
            </tr>
            @endforeach
        @endif

        <tr>
            <td colspan="4" class="align-top pt-2">
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
                @if ($document->detraction)
                <p>
                    <span class="font-bold">
                    Operación sujeta al Sistema de Pago de Obligaciones Tributarias
                    </span>
                </p>
                @endif
                @if ($customer->department_id == 16)
                    <div>
                        <center>
                            Representación impresa del Comprobante de Pago Electrónico.
                            <br/>Esta puede ser consultada en:
                            <br/><b>{!! url('/buscar') !!}</b>
                            <br/> "Bienes transferidos en la Amazonía
                            <br/>para ser consumidos en la misma".
                        </center>
                    </div>
                @endif
                @foreach($document->additional_information as $information)
                    @if ($information)
                        @if ($loop->first)
                            <strong>Información adicional</strong>
                        @endif
                        <p>{{ $information }}</p>
                    @endif
                @endforeach
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
                @if($payments->count())
                    <table class="full-width">
                        <tr>
                            <td>
                                <strong>PAGOS:</strong>
                            </td>
                        </tr>
                            @php
                                $payment = 0;
                            @endphp
                            @foreach($payments as $row)
                                <tr>
                                    <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
                                </tr>
                                @php
                                    $payment += (float) $row->payment;
                                @endphp
                            @endforeach
                        </tr>
                        <tr>
                            <td>
                                <strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}
                            </td>
                        </tr>

                    </table>
                @endif
            </td>
            <td colspan="4" width="30%" class="align-top">
                <table class="full-width">
                    @if($document->total_exportation > 0)
                        <tr>
                            <td class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_free > 0)
                        <tr>
                            <td class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_unaffected > 0)
                        <tr>
                            <td class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_exonerated > 0)
                        <tr>
                            <td class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_taxed > 0)
                        <tr>
                            <td class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_discount > 0)
                        <tr>
                            <td class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
                        </tr>
                    @endif
                    @if($document->total_plastic_bag_taxes > 0)
                        <tr>
                            <td class="text-right font-bold">ICBPER: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
                    </tr>

                    @if($document->perception)
                        <tr>
                            <td class="text-right font-bold"> IMPORTE TOTAL: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">PERCEPCIÓN: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
                        </tr>
                    @endif

                    @if($balance < 0)

                        <tr>
                            <td class="text-right font-bold">VUELTO: {{ $document->currency_type->symbol }}</td>
                            <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
                        </tr>

                    @endif
                    @if($document->hash)
                        <tr>
                            <td class="pt-10 desc text-right" colspan="2">Código Hash: {{ $document->hash }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>
