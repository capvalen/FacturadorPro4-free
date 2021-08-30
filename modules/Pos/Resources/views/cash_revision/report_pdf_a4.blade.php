<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte POS - {{ $data['cash']['seller'] }}
        - {{ $data['cash']['date_opening'] }} {{ $data['cash']['time_opening'] }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-spacing: 0;
            border: 1px solid black;
        }

        .celda {
            text-align: center;
            padding: 5px;
            border: 0.1px solid black;
        }

        th {
            padding: 5px;
            text-align: center;
            border: 0.1px solid #0088cc;
        }

        .title {
            font-weight: bold;
            padding: 5px;
            font-size: 20px !important;
            text-decoration: underline;
        }

        p > strong {
            margin-left: 5px;
            font-size: 12px;
        }

        thead tr th {
            font-weight: bold;
            background: #0088cc;
            color: white;
            text-align: center;
        }

        .width-custom {
            width: 50%
        }
    </style>
</head>
<body>
<div>
    <p align="center" class="title"><strong>Reporte Punto de Venta</strong></p>
</div>
<div style="margin-top:20px; margin-bottom:20px;">
    <table>
        <tr>
            <td class="td-custom width-custom">
                <p><strong>Empresa: </strong>{{ $data['company']['name'] }}</p>
            </td>
            <td class="td-custom">
                <p><strong>Fecha reporte: </strong>{{date('Y-m-d')}}</p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Ruc: </strong>{{ $data['company']['number'] }}</p>
            </td>
            <td class="width-custom">
                <p><strong>Establecimiento: </strong>{{ $data['establishment']['address'] }}</p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Vendedor: </strong>{{ $data['cash']['seller'] }}</p>
            </td>
            <td class="td-custom">
                <p><strong>Fecha y hora
                        apertura: </strong>{{ $data['cash']['date_opening'] }} {{ $data['cash']['time_opening'] }}</p>
            </td>
        </tr>
        <tr>
            <td class="td-custom">
                <p><strong>Estado de caja: </strong>{{ $data['cash']['state'] }}</p>
            </td>
            @if(!$data['cash']['state'])
                <td class="td-custom">
                    <p><strong>Fecha y hora
                            cierre: </strong>{{ $data['cash']['date_closed'] }} {{ $data['cash']['time_closed'] }}</p>
                </td>
            @endif
        </tr>
        <tr>
            <td colspan="2" class="td-custom">
                <p><strong>Montos de operación: </strong></p>
            </td>
        </tr>
        {{--                <tr>--}}
        {{--                    <td class="td-custom">--}}
        {{--                        <p><strong>Saldo inicial: </strong>S/. {{number_format($cash->beginning_balance, 2, ".", "")}}</p>--}}
        {{--                    </td>--}}
        {{--                    <td  class="td-custom">--}}
        {{--                        <p><strong>Ingreso: </strong>S/. {{number_format($cash_income, 2, ".", "")}} </p>--}}
        {{--                    </td>--}}
        {{--                </tr>--}}
        {{--                <tr>--}}
        {{--                    <td  class="td-custom">--}}
        {{--                        <p><strong>Saldo final: </strong>S/. {{number_format($cash_final_balance, 2, ".", "")}} </p>--}}
        {{--                    </td>--}}
        {{--                    <td  class="td-custom">--}}
        {{--                        <p><strong>Egreso: </strong>S/. {{number_format($cash_egress, 2, ".", "")}} </p>--}}
        {{--                    </td>--}}
        {{--                </tr>--}}
    </table>
</div>
    <div class="">
        <div class=" ">

{{--            <table>--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th>#</th>--}}
{{--                    <th>Descripcion</th>--}}
{{--                    <th>Suma</th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                @foreach($methods_payment as $item)--}}

{{--                    <tr>--}}
{{--                        <td class="celda">{{ $loop->iteration }}</td>--}}
{{--                        <td class="celda">{{ $item->name }}</td>--}}
{{--                        <td class="celda">{{ number_format($item->sum, 2, ".", "")  }}</td>--}}

{{--                    </tr>--}}

{{--                @endforeach--}}
{{--                </tbody>--}}

{{--            </table>--}}

            <table>
                <thead>
                <tr>
                    <th>#</th>
                    {{--                            <th>Tipo documento</th>--}}
                    <th>Documento</th>
                    <th>Fecha emisión</th>
                    <th>Cliente/Proveedor</th>
                    <th>N° Documento</th>
                    <th>Moneda</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="7">Ventas</td>
                </tr>
                @foreach($data['documents'] as $row)
                    <tr>
                        <td class="celda">{{ $loop->iteration }}</td>
                        {{--                                <td class="celda">{{ $type_transaction }}</td>--}}
                        {{--                                <td class="celda">{{ $document_type_description }}</td>--}}
                        <td class="celda">{{ $row['number'] }}</td>
                        <td class="celda">{{ $row['date_of_issue'] }}</td>
                        <td class="celda">{{ $row['$customer_name'] }}</td>
                        <td class="celda">{{ $row['customer_number'] }}</td>
                        <td class="celda">{{ $row['currency_type_id'] }}</td>
                        <td class="celda">{{ $row['total'] }}</td>

                    </tr>
                @endforeach

                <tr>
                    <td colspan="7">Servicios técnicos</td>
                </tr>
                @foreach($data['technical_services'] as $row)
                    <tr>
                        <td class="celda">{{ $loop->iteration }}</td>
                        {{--                                <td class="celda">{{ $type_transaction }}</td>--}}
                        {{--                                <td class="celda">{{ $document_type_description }}</td>--}}
                        <td class="celda">{{ $row['number'] }}</td>
                        <td class="celda">{{ $row['date_of_issue'] }}</td>
                        <td class="celda">{{ $row['customer_name'] }}</td>
                        <td class="celda">{{ $row['customer_number'] }}</td>
                        <td class="celda">{{ $row['currency_type_id'] }}</td>
                        <td class="celda">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
{{--@else--}}
{{--    <div class="callout callout-info">--}}
{{--        <p>No se encontraron registros.</p>--}}
{{--    </div>--}}
{{--@endif--}}
</body>
</html>
