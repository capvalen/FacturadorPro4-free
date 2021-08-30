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
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:50%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@endif
<table class="full-width">
    <tr>
        <td width="60%" class="text-center pr-2">
            @if($company->logo)
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 100px;">
                </div>
            @endif
            <br>
            <h5 class="font-bold text-upp">{{ $company->name }}</h5>
            <hr>
            <h6 style="text-transform: uppercase;">
                {{ ($establishment->address !== '-')? $establishment->address.',' : '' }}
                {{ ($establishment->district_id !== '-')? $establishment->district->description : '' }}
                {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
            </h6>

            @isset($establishment->trade_address)
                <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
            @endisset

            <h6>{{ ($establishment->telephone !== '-')? 'Telf. '.$establishment->telephone : '' }}</h6>

            <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

            @isset($establishment->web_address)
                <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
            @endisset

            @isset($establishment->aditional_information)
                <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
            @endisset
        </td>
        <td width="40%" class="border-box py-2 px-2 text-center">
            <h3 class="font-bold">{{ 'RUC '.$company->number }}</h3>
            <h3 class="text-center font-bold">{{ $document->document_type->description }}</h3>
            <br>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-2">
    <tr>
        <td width="95%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td colspan="2" class="font-lg">
                        <strong>SEÑOR(ES): </strong>
                        {{ $customer->name }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="font-lg">
                        <strong>DIRECCIÓN: </strong>
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
                @if($customer->telephone !== '-')
                <tr>
                    <td colspan="2" class="font-lg">
                        <strong>TELÉFONO:</strong>
                        {{ $customer->telephone}}
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="2"  class="font-lg">
                        <strong>RUC: </strong>
                        {{$customer->number}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2"  class="font-lg">
                        <strong>MONEDA: </strong>
                        <span class="text-upp">{{ $document->currency_type->description }}</span>
                    </td>
                </tr>
                <tr>
                    <td  class="font-lg">
                        <strong>FECHA: </strong>
                        {{$document->date_of_issue->format('Y-m-d')}}
                    </td>
                    <td  class="font-lg">
                        @if($invoice)
                            <strong>FECHA VENC.:</strong>
                            {{$invoice->date_of_due->format('Y-m-d')}}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td width="5%" class="p-0 m-0">
            <img src="data:image/png;base64, {{ $document->qr }}" class="p-0 m-0" style="width: 120px;" />
        </td>
    </tr>
</table>
<table class="full-width my-3 text-center" border="1">
    <tr>
        <td width="16.6%" class="desc">UBIGEO</td>
        <td width="16.6%" class="desc">O/C</td>
        <td width="16.6%" class="desc">CONDICIONES DE PAGO</td>
        <td width="16.6%" class="desc">VENDEDOR</td>
        <td width="16.6%" class="desc">GUÍA DE REMISIÓN</td>
        <td width="16.6%" class="desc">AGENCIA DE TRANSPORTE</td>
    </tr>
    <tr>
        <td class="desc"></td>
        <td class="desc"></td>
        <td class="desc">
            @php
                $payment = 0;
            @endphp
            @foreach($payments as $row)
                {{ $row->payment_method_type->description }}
            @endforeach
        </td>
        <td class="desc">{{ $document->user->name }}</td>
        <td class="desc">
            @if ($document->guides)
                @foreach($document->guides as $guide)
                    {{ $guide->number }}
                @endforeach
            @endif

            @if ($document->reference_guides)
                @foreach($document->reference_guides as $guide)
                    {{ $guide->number }}
                @endforeach
            @endif
        </td>
        <td class="desc"></td>
    </tr>
</table>

<table class="full-width my-0 py-0" border="1">
    <thead >
        <tr class="mt-0">
            <th class="border-bottom text-center py-1 desc" width="10%">CÓDIGO</th>
            <th class="border-bottom text-center py-1 desc" width="10%">MARCA</th>
            <th class="border-bottom text-center py-1 desc" width="">DESCRIPCIÓN</th>
            <th class="border-bottom text-center py-1 desc" width="10%">CANT.</th>
            <th class="border-bottom text-center py-1 desc" width="10%">U.M.</th>
            <th class="border-bottom text-center py-1 desc" width="10%">P.U</th>
            <th class="border-bottom text-center py-1 desc" width="10%">IMPORTE</th>
        </tr>
    </thead>
    <tbody class="">
        @foreach($document->items as $row)
            <tr>
                <td class="p-1 text-center align-top desc">{{ $row->item->internal_id }}</div></td>
                <td class="p-1 text-center align-top desc">{{ $row->m_item->brand != null ? $row->m_item->brand->name : '' }}</div></td>
                <td class="p-1 text-left align-top desc text-upp">
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
                <td class="p-1 text-center align-top desc">
                    @if(((int)$row->quantity != $row->quantity))
                        {{ $row->quantity }}
                    @else
                        {{ number_format($row->quantity, 0) }}
                    @endif
                </td>
                <td class="p-1 text-center align-top desc">{{ $row->item->unit_type_id }}</td>
                <td class="p-1 text-right align-top desc">{{ number_format($row->unit_price, 2) }}</td>
                <td class="p-1 text-right align-top desc">{{ number_format($row->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="full-width border-box my-2">
    <tr>
        <td class="text-upp p-2">SON:
            @foreach(array_reverse( (array) $document->legends) as $row)
                @if ($row->code == "1000")
                    {{ $row->value }} {{ $document->currency_type->description }}
                @else
                    {{$row->code}}: {{ $row->value }}
                @endif
            @endforeach
        </td>
    </tr>
</table>
<table class="full-width border-box my-2">
    <tr>
        <td class="text-upp p-2">OBSERVACIONES:
            @if($document->additional_information)
                @foreach($document->additional_information as $information)
                    @if ($information)
                        {{ $information }}
                    @endif
                @endforeach
            @endif
        </td>
    </tr>
</table>
<table class="full-width mt-10 mb-10 border-bottom">
    <tr>
        <th class="border-box text-center py-1 desc">IMPORTE BRUTO</th>
        <th class="border-box text-center py-1 desc">DESCUENTOS</th>
        <th class="border-box text-center py-1 desc">TOTAL VALOR VENTA</th>
        <th class="border-box text-center py-1 desc">I.G.V. 18%</th>
        <th class="border-box text-center py-1 desc">TOTAL PRECIO VENTA</th>
        <th class="border-box text-center py-1 desc">PAGO A CUENTA</th>
        <th class="border-box text-center py-1 desc">NETO A PAGAR</th>
    </tr>
    <tr>
        <td class="border-box text-center py-1 desc">
            @if($document->total_taxed > 0)
                {{ $document->currency_type->symbol }} {{ number_format($document->total_taxed, 2) }}
            @endif
        </td>
        <td class="border-box text-center py-1 desc">
            @if($document->total_discount > 0)
                {{ $document->currency_type->symbol }} {{ number_format($document->total_discount, 2) }}
            @endif
        </td>
        <td class="border-box text-center py-1 desc">
            @if($document->total_taxed > 0)
                {{ $document->currency_type->symbol }} {{ number_format($document->total_taxed, 2) }}
            @endif
        </td>
        <td class="border-box text-center py-1 desc">
            {{ $document->currency_type->symbol }} {{ number_format($document->total_igv, 2) }}
        </td>
        <td class="border-box text-center py-1 desc">
            {{ $document->currency_type->symbol }} {{ number_format($document->total, 2) }}
        </td>
        <td class="border-box text-center py-1 desc"></td>
        <td class="border-box text-center py-1 desc">
            {{ $document->currency_type->symbol }} {{ number_format($document->total, 2) }}
        </td>
    </tr>
</table>
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
<table class="full-width">
    <tr>
        <td class="text-center desc">Representación Impresa de {{ isset($document->document_type) ? $document->document_type->description : 'Comprobante Electrónico'  }} {{ isset($document->hash) ? 'Código Hash: '.$document->hash : '' }} <br>Para consultar el comprobante ingresar a {!! url('/buscar') !!}</td>
    </tr>
</table>

</body>
</html>
