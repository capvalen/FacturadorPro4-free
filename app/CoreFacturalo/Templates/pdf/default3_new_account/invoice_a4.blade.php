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

    //calculate items
    $allowed_items = 94 - (\App\Models\Tenant\BankAccount::all()->count())*2;
    $quantity_items = $document->items()->count();
    $cycle_items = $allowed_items - ($quantity_items * 3);
    $total_weight = 0;

@endphp
<html>
<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:30%;">
        <img
            src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}"
            alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="20%">
                <div class="company_logo_box">
                    <img
                        src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}"
                        alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
        @else
            <td width="20%">
                {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px">--}}
            </td>
        @endif
        <td width="40%" class="pl-3">
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
        <td width="40%" class="border-box py-2 px-2 text-center">
            <h3 class="font-bold">{{ 'R.U.C. '.$company->number }}</h3>
            <h3 class="text-center font-bold">{{ $document->document_type->description }}</h3>
            <br>
            <h3 class="text-center font-bold">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-3">
    <tr>
        <td width="47%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td class="font-sm" width="80px">
                        <strong>Razón Social</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        {{ $customer->name }}
                    </td>
                </tr>
                <tr>
                    <td class="font-sm" width="80px">
                        <strong>{{$customer->identity_document_type->description}}</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        {{$customer->number}}
                    </td>
                </tr>
                <tr>
                    <td class="font-sm" width="80px">
                        <strong>Dirección</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        @if ($customer->address !== '')
                            <span style="text-transform: uppercase;">
                                {{ $customer->address }}
                                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                            </span>
                        @endif
                    </td>
                </tr>

                @if(!is_null($document_base))
                    <tr>
                        <td class="font-sm font-bold" width="80px">Doc. Afectado</td>
                        <td class="font-sm" width="8px">:</td>
                        <td class="font-sm">{{ $affected_document_number }}</td>
                    </tr>
                    <tr>
                        <td class="font-sm font-bold" width="80px">Tipo de nota</td>
                        <td class="font-sm">:</td>
                        <td class="font-sm">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
                    </tr>
                    <tr>
                        <td class="font-sm font-bold" width="80px">Descripción</td>
                        <td class="font-sm">:</td>
                        <td class="font-sm">{{ $document_base->note_description }}</td>
                    </tr>
                @endif
            </table>
        </td>
        <td width="3%"></td>
        <td width="50%" class="border-box pl-1 ">
            <table class="full-width">


                <tr>
                    <td class="font-sm" width="90px">
                        <strong>Fecha Emisión</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        {{ $document->date_of_issue->format('Y-m-d') }}
                    </td>
                    <td class="font-sm" width="70px">
                        <strong>H. Emisión</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        {{ $document->time_of_issue }}
                    </td>
                </tr>

                <tr>
                    @if($invoice)
                        <td class="font-sm" width="90px">
                            <strong>Fecha de Vcto</strong>
                        </td>
                        <td class="font-sm" width="8px">:</td>
                        <td class="font-sm">
                            {{$invoice->date_of_due->format('Y-m-d')}}
                        </td>
                    @endif

                    <td class="font-sm" width="70px">
                        <strong>Moneda</strong>
                    </td>
                    <td class="font-sm" width="8px">:</td>
                    <td class="font-sm">
                        {{ $document->currency_type->description }}
                    </td>
                </tr>

                <tr>
                    @if($document->purchase_order)
                        <td class="font-sm" width="90px">
                            <strong>Orden de Compra</strong>
                        </td>
                        <td class="font-sm" width="8px">:</td>
                        <td class="font-sm">
                            {{ $document->purchase_order }}
                        </td>
                    @endif

                    @if($document->payments()->count() > 0)
                        <td class="font-sm" width="70px">
                            <strong>F. Pago</strong>
                        </td>
                        <td class="font-sm" width="8px">:</td>
                        <td class="font-sm">
                            {{ $document->payments()->first()->payment_method_type->description }}
                            - {{ $document->currency_type_id }} {{ $document->payments()->first()->payment }}
                        </td>
                    @endif
                </tr>

                <tr>
                    @if($document->guides)
                        <td class="font-sm" width="100px">
                            <strong>Guía de Remisión</strong>
                        </td>
                        <td class="font-sm" width="8px">:</td>
                        <td class="font-sm" colspan="4">
                            @foreach ($document->guides as $item)
                                {{ $item->document_type_description }}:  {{ $item->number }}<br>
                            @endforeach
                        </td>
                    @endif
                </tr>


            </table>
        </td>
        {{-- <td width="5%" class="p-0 m-0">
            <img src="data:image/png;base64, {{ $document->qr }}" class="p-0 m-0" style="width: 120px;" />
        </td> --}}
    </tr>
</table>
<table class="full-width my-2 text-center" border="0">
    <tr>
        <td class="desc"></td>
    </tr>
</table>


<table class="full-width mt-0 mb-0">
    <thead>
    <tr class="">
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="12%">CÓDIGO</th>
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="8%">U.M.</th>
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="40%">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-1 desc" class="cell-solid" width="12%">P.UNIT</th>
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="8%">DCTO.</th>
        <th class="border-top-bottom text-center py-1 desc" class="cell-solid" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody class="">
    @foreach($document->items as $row)
        <tr>
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
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!}
                @endif

                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif

                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        @if($attr->attribute_type_id === '5032')
                            @php
                                $total_weight += $attr->value * $row->quantity;
                            @endphp
                        @endif
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
            <td class="p-1 text-right align-top desc cell-solid-rl">{{ number_format($row->unit_price, 2) }}</td>
            <td class="p-1 text-right align-top desc cell-solid-rl">
                @if($row->discounts)
                    @php
                        $total_discount_line = 0;
                        foreach ($row->discounts as $disto) {
                            $total_discount_line = $total_discount_line + $disto->amount;
                        }
                    @endphp
                    {{ number_format($total_discount_line, 2) }}
                @endif
            </td>
            <td class="p-1 text-right align-top desc cell-solid-rl">{{ number_format($row->total, 2) }}</td>
        </tr>

    @endforeach

    @for($i = 0; $i < $cycle_items; $i++)
        <tr>
            <td class="p-1 text-center align-top desc cell-solid-rl"></td>
            <td class="p-1 text-center align-top desc cell-solid-rl">
            </td>
            <td class="p-1 text-center align-top desc cell-solid-rl"></td>
            <td class="p-1 text-left align-top desc text-upp cell-solid-rl">
            </td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
            <td class="p-1 text-right align-top desc cell-solid-rl">
            </td>
            <td class="p-1 text-right align-top desc cell-solid-rl"></td>
        </tr>
    @endfor

    <tr>
        <td class="p-1 text-left align-top desc cell-solid" colspan="3"><strong>
                VENDEDOR:</strong> {{ $document->user->name }}</td>
        <td class="p-1 text-left align-top desc cell-solid font-bold">
            SON:
            @foreach(array_reverse( (array) $document->legends) as $row)
                @if ($row->code == "1000")
                    {{ $row->value }} {{ $document->currency_type->description }}
                @else
                    {{$row->code}}: {{ $row->value }}
                @endif
            @endforeach
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            OP. GRAVADA {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_taxed, 2) }}</td>
    </tr>

    <tr>
        <td class="p-1 text-left align-top desc cell-solid" colspan="3" rowspan="6">
            @php
                $total_packages = $document->items()->sum('quantity');

            @endphp

            <strong> Total bultos:</strong>
            @if(((int)$total_packages != $total_packages))
                {{ $total_packages }}
            @else
                {{ number_format($total_packages, 0) }}
            @endif
            <br>

            <strong> Total Peso:</strong>
            {{$total_weight}} KG
            <br>

            <strong> Observación:</strong>
            @foreach($document->additional_information as $information)
                @if ($information)
                    {{ $information }} <br>
                @endif
            @endforeach

            <br>
        </td>
        <td class="p-1 text-center align-top desc cell-solid " rowspan="6">

            <img src="data:image/png;base64, {{ $document->qr }}" class="p-0 m-0" style="width: 120px;"/><br>
            Código Hash: {{ $document->hash }}

        </td>

        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            OP. INAFECTAS {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
    </tr>


    <tr>
        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            OP. EXONERADAS {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
    </tr>

    <tr>
        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            OP. GRATUITAS {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_free, 2) }}</td>
    </tr>
    <tr>
        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            TOTAL DCTOS. {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_discount, 2) }}</td>
    </tr>
    <tr>

        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            I.G.V. {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total_igv, 2) }}</td>
    </tr>
    <tr>
        <td class="p-1 text-right align-top desc cell-solid font-bold" colspan="2">
            TOTAL A PAGAR. {{$document->currency_type->symbol}}
        </td>
        <td class="p-1 text-right align-top desc cell-solid font-bold">{{ number_format($document->total, 2) }}</td>
    </tr>
    </tbody>

</table>
@if($document != null)

    <table class="full-width border-box my-2">
        @foreach($accounts as $account)
            <tr>
                <th class="p-1">Banco</th>
                <th class="p-1">Moneda</th>
                <th class="p-1">Código de Cuenta Interbancaria</th>
                <th class="p-1">Código de Cuenta</th>
            </tr>
            <tr>
                <td class="text-center">{{$account->bank->description}}</td>
                <td class="text-center text-upp">{{$account->currency_type->description}}</td>
                <td class="text-center">
                    @if($account->cci)
                        {{$account->cci}}
                    @endif
                </td>
                <td class="text-center">{{$account->number}}</td>
            </tr>
        @endforeach
    </table>
@endif
</body>
</html>
