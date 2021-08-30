@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $details = $document->details;
    $document_base = $document->invoice;
    //$optional = $document->optional;
    $document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $document_type_description_array = [
        '01' => 'FACTURA',
        '03' => 'BOLETA DE VENTA',
        '07' => 'NOTA DE CREDITO',
        '08' => 'NOTA DE DEBITO',
    ];
    $identity_document_type_description_array = [
        '-' => 'S/D',
        '0' => 'S/D',
        '1' => 'DNI',
        '6' => 'RUC',
    ];
    //$document_type_description = $document_type_description_array[$document->document_type_code];
@endphp
<html>
<head>
    <title>{{ $document_number }}</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 12px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-xsm {
            font-size: 10px;
        }
        .font-sm {
            font-size: 12px;
        }
        .font-lg {
            font-size: 13px;
        }
        .font-xlg {
            font-size: 16px;
        }
        .font-xxlg {
            font-size: 22px;
        }
        .font-bold {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        .voucher-company-right {
            border: 1px solid #333;
            padding-top: 15px;
            padding-bottom: 15px;
            margin-bottom: 10px;
        }
        .voucher-company-right tbody tr:first-child td {
            padding-top: 10px;
        }
        .voucher-company-right tbody tr:last-child td {
            padding-bottom: 10px;
        }
        .voucher-information {
            border: 1px solid #333;
        }
        .voucher-information.top-note, .voucher-information.top-note tbody tr td {
            border-top: 0;
        }
        .voucher-information tbody tr td {
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: top;
        }
        .voucher-information-left tbody tr td {
            padding: 3px 10px;
            vertical-align: top;
        }
        .voucher-information-right tbody tr td {
            padding: 3px 10px;
            vertical-align: top;
        }
        .voucher-details {
        }
        .voucher-details thead {
            background-color: #f5f5f5;
        }
        .voucher-details thead tr th {
            /*border-top: 1px solid #333;*/
            /*border-bottom: 1px solid #333;*/
            padding: 5px 10px;
        }
        .voucher-details thead tr th:first-child {
            border-left: 1px solid #333;
        }
        .voucher-details thead tr th:last-child {
            border-right: 1px solid #333;
        }
        .voucher-details tbody tr td {
            /*border-bottom: 1px solid #333;*/
        }
        .voucher-details tbody tr td:first-child {
            border-left: 1px solid #333;
        }
        .voucher-details tbody tr td:last-child {
            border-right: 1px solid #333;
        }
        .voucher-details tbody tr td {
            padding: 5px 10px;
            vertical-align: middle;
        }
        .voucher-details tfoot tr td {
            padding: 3px 10px;
        }
        .voucher-totals {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .voucher-totals tbody tr td {
            padding: 3px 10px;
            vertical-align: top;
        }
        .voucher-footer {
            margin-bottom: 30px;
        }
        .voucher-footer tbody tr td{
            border-top: 1px solid #333;
            padding: 3px 10px;
        }
        .company_logo {
            min-width: 150px;
            max-width: 100%;
            height: auto;
        }
        .pt-1 {
            padding-top: 1rem;
        }
    </style>
</head>
<body>
<table class="voucher-company">
    <tr>
        @if($company->logo)
            <td width="25%">
                <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo">
            </td>
        @else
            <td width="20%">
                <img src="{{ asset('logo/logo.jpg') }}" alt="" class="company_logo" style="max-width: 200px">
            </td>
        @endif
        <td width="100%">
            <table class="voucher-company-left">
                <tbody>
                <tr><td class="text-left font-xxlg font-bold">{{ $company->name }}</td></tr>
                <tr><td class="text-left font-xl font-bold">{{ 'RUC '.$company->number }}</td></tr>
                @if($establishment)
                    <tr><td class="text-left font-lg">{{ $establishment->address }}</td></tr>
                    <tr><td class="text-left font-lg">{{ ($establishment->email != '-')? $establishment->email : '' }}</td></tr>
                    <tr><td class="text-left font-lg font-bold">{{ ($establishment->telephone != '-')? $establishment->telephone : '' }}</td></tr>
                @endif
                </tbody>
            </table>
        </td>
        <td width="30%">
            <table class="voucher-company-right">
                <tbody>
                <tr><td class="text-center font-lg">{{ $document->document_type->description }}</td></tr>
                <tr><td class="text-center font-xlg font-bold">{{ $document_number }}</td></tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<table class="voucher-information">
    <tr>
        <td width="55%">
            <table class="voucher-information-left">
                <tbody>
                <tr>
                    <td width="50%">Fecha de emisión: </td>
                    <td width="50%">{{ $document->date_of_issue->format('d/m/Y') }}</td>
                </tr>
                @if($document_base->date_of_due)
                    <tr>
                        <td width="50%">Fecha de vencimiento: </td>
                        <td width="50%">{{ $document_base->date_of_due->format('d/m/Y') }}</td>
                    </tr>
                @endif
                <tr>
                    <td width="20%">Cliente:</td>
                    <td width="80%">{{ $customer->name }}</td>
                </tr>
                <tr>
                    <td width="20%">{{ $customer->identity_document_type->description }}:</td>
                    <td width="80%">{{ $customer->number }}</td>
                </tr>
                @if ($customer->address !== '')
                    <tr>
                        <td width="20%">Dirección:</td>
                        <td width="80%">{{ $customer->address }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </td>
        <td width="45%">
            <table class="voucher-information-right">
                <tbody>
                @if ($document->purchase_order)
                    <tr>
                        <td width="50%">Orden de Compra: </td>
                        <td width="50%">{{ $document->purchase_order }}</td>
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
                </tbody>
            </table>
        </td>
    </tr>
</table>
<table class="voucher-details">
    <thead>
    <tr>
        <th class="text-center" width="80px">CANT.</th>
        <th width="60px">UNIDAD</th>
        <th>DESCRIPCIÓN</th>
        <th class="text-right" width="80px">P.UNIT</th>
        <th class="text-right" width="80px">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($details as $row)
        <tr>
            <td class="text-center">{{ $row->quantity }}</td>
            <td>{{ $row->item->unit_type_id }}</td>
            <td>
                {!! $row->item->description !!}
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->name !!} : {{ $attr->value }}
                    @endforeach
                @endif
            </td>
            <td  class="text-right" >{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right">{{ number_format($row->total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot style="border-top: 1px solid #333;">
    <tr>
        <td colspan="5" class="font-lg font-bold"  style="padding-top: 2rem;">Son: {{ $document->number_to_letter }} {{ $document->currency_type->description }}</td>
    </tr>
    </tfoot>
</table>
<table class="voucher-totals">
    <tbody>
    <tr>
        <td width="35%">
            <table class="voucher-totals-left">
                {{--<tbody>--}}
                <tr>
                    <td class="text-center">
                        <img class="qr_code" src="data:image/png;base64, {{ $document->qr }}" />
                    </td>
                </tr>
                <tr><td class="text-center">Código Hash</td></tr>
                <tr><td class="text-center">{{ $document->hash }}</td></tr>
                {{--</tbody>--}}
            </table>
        </td>
        <td width="65%">
            <table class="voucher-totals-right">
                <tbody>
                @if($document->total_free > 0)
                    <tr>
                        <td class="text-right font-lg font-bold" width="70%">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total_free, 2) }}</td>
                    </tr>
                @endif
                @if($document->total_unaffected > 0)
                    <tr>
                        <td class="text-right font-lg font-bold" width="70%">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total_unaffected, 2) }}</td>
                    </tr>
                @endif
                @if($document->total_exonerated > 0)
                    <tr>
                        <td class="text-right font-lg font-bold" width="70%">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total_exonerated, 2) }}</td>
                    </tr>
                @endif
                @if($document->total_taxed > 0)
                    <tr>
                        <td class="text-right font-lg font-bold" width="70%">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                        <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total_taxed, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-right font-lg font-bold" width="70%">IGV: {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total_igv, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right font-lg font-bold" width="70%">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-lg font-bold" width="30%">{{ number_format($document->total, 2) }}</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>