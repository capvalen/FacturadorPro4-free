@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $left =  ($document->series) ? $document->series : $document->prefix;
    $tittle = $left.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
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
            <h5 class="text-center">NOTA DE VENTA</h5>
            <h3 class="text-center">{{ $tittle }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-2">
    <tr>
        <td width="100%" class="border-box pl-3">
            <table class="full-width">
                <tr>
                    <td class="">
                        <strong>SEÑOR(ES): </strong>
                        {{ $customer->name }}
                    </td>
                    <td class="">
                    </td>
                </tr>
                <tr>
                    <td  class="">
                        <strong>RUC: </strong>
                        {{$customer->number}}
                    </td>s
                    <td  class="">

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
                @if($customer->telephone !== null)
                <tr>
                    <td colspan="2">
                        <strong>TELÉFONO:</strong>
                        {{ $customer->telephone}}
                    </td>
                </tr>
                @endif
                <tr>
                    <td  class="">
                        <strong>FECHA: </strong>
                        {{$document->date_of_issue->format('Y-m-d')}}
                    </td>
                    <td  class="">
                        <strong>MONEDA: </strong>
                        <span class="text-upp">{{ $document->currency_type->description }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="full-width my-2 text-center" border="1">
    <tr>
        <td width="16.6%" class="desc">UBIGEO</td>
        <td width="16.6%" class="desc">O/C</td>
        <td width="16.6%" class="desc">CONDICIONES DE PAGO</td>
        <td width="16.6%" class="desc">VENDEDOR</td>
        <td width="16.6%" class="desc">ESTADO</td>
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
            @if ($document->total_canceled)
                CANCELADO
            @else
                PENDIENTE DE PAGO
            @endif
        </td>
    </tr>
</table>

@if ($document->guides)
<br/>
{{--<strong>Guías:</strong>--}}
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
                <td class="p-1 text-center align-top desc">{{ $row->relation_item->internal_id }}</div></td>
                <td class="p-1 text-center align-top desc">{{ $row->relation_item->brand != null ? $row->relation_item->brand->name : '' }}</div></td>
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
</body>
</html>
