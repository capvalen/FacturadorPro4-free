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

<div style="border: 1px solid #000;height: 455px;padding-left: -1px;width:100%;position: all ;display: table; z-index: 1;">
@if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: all; text-align: center; top:40%; z-index: 2">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
@else
    @if($company->logo)
        <div class="company_logo_box" style="position: all; text-align: center; margin-top: 90px">
            <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="logo" class="" style="opacity: 0.1;">
        </div>
    @endif
@endif
</div>

</body>