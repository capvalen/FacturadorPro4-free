@php
    $establishment = $document->user->establishment;
    $customer = $document->customer;
    $tittle = str_pad($document->id, 8, '0', STR_PAD_LEFT);
@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
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
                <h6>
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
            <h5 class="text-center">SERVICIO TÉCNICO</h5>
            <h3 class="text-center">{{ $tittle }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="15%">Cliente:</td>
        <td width="45%">{{ $customer->name }}</td>
        <td width="25%">Fecha de emisión:</td>
        <td width="15%">{{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>{{ $customer->identity_document_type->description }}:</td>
        <td>{{ $customer->number }}</td>
      
    </tr>
    @if ($customer->address !== '')
    <tr>
        <td class="align-top">Dirección:</td>
        <td colspan="">
            {{ $customer->address }}
            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
        </td>
    </tr>
    @endif 
    <tr>
        <td class="align-top">Celular:</td>
        <td colspan="3">
            {{ $document->cellphone }} 
        </td>
    </tr>
    <tr>
        <td class="align-top">N° Serie:</td>
        <td colspan="3">
            {{ $document->serial_number }} 
        </td>
    </tr>
</table>  


<table class="full-width mt-4 mb-5">
    <tr>
        <td ><b>Descripción:</b></td> 
    </tr>
    <tr>
        <td>{{ $document->description }}</td>
    </tr> 
    <tr>
        <td ><b> Estado:</b></td>
    </tr>
    <tr>
        <td >{{ $document->state }}</td>
    </tr>
    
    <tr>
        <td><b>Motivo:</b></td>
    </tr>
    <tr>
        <td >{{ $document->reason }}</td>
    </tr> 
    @if($document->activities)
    <tr>
        <td><b>Actividades realizadas:</b></td>
    </tr>
    <tr>
        <td>{{ $document->activities }}</td>
    </tr> 
    @endif
</table>  

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey"> 
    </tr>
    </thead>
    <tbody>
        <tr> 
        </tr>
        <tr>
            <td colspan="4" class="text-right font-bold mb-3">COSTO DEL SERVICIO: </td>
            <td class="text-right font-bold">{{ number_format($document->cost, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right font-bold">PAGO ADELANTADO: </td>
            <td class="text-right font-bold">{{ number_format($document->prepayment, 2) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-right font-bold">SALDO A PAGAR: </td>
            <td class="text-right font-bold">{{ number_format($document->cost - $document->prepayment, 2) }}</td>
        </tr>
    </tbody>
</table>
</body>
</html>