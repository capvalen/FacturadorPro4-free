<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte POS - {{$data['cash_user_name']}}
        - {{$data['cash_date_opening']}} {{$data['cash_time_opening']}}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
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
            border-color: #0088cc;
            border: 0.1px solid black;
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

        thead {
            font-weight: bold;
            background: #0088cc;
            color: white;
            text-align: center;
        }

        /* .td-custom { line-height: 0.1em; } */
        .width-custom {
            width: 50%
        }
    </style>
</head>
<body>
<div>
    <p align="center" class="title"><strong>Reporte Punto de Venta</strong></p>
</div>
<div style="margin-top:5px; margin-bottom:10px;">
    <p><strong>Empresa: </strong>{{$data['company_name']}}</p>
    <p><strong>Fecha reporte: </strong>{{date('Y-m-d')}}</p>
    <p><strong>Ruc: </strong>{{$data['company_number']}}</p>
    <p><strong>Establecimiento: </strong>{{$data['establishment_address']}}
        - {{$data['establishment_department_description']}} - {{$data['establishment_district_description']}}</p>
    <p><strong>Vendedor: </strong>{{$data['cash_user_name']}}</p>
    <p><strong>Fecha y hora apertura: </strong>{{$data['cash_date_opening']}} {{$data['cash_time_opening']}}</p>
    <p><strong>Estado de caja: </strong>{{($data['cash_state']) ? 'Aperturada':'Cerrada'}}</p>
    <p><strong>Fecha y hora cierre: </strong>{{$data['cash_date_closed']}} {{$data['cash_time_closed']}}</p>
    <p><strong>Montos de operación </strong></p>
    <p><strong>Saldo inicial: </strong>S/. {{$data['cash_beginning_balance']}}</p>
    <p><strong>Ingreso: </strong>S/. {{ $data['cash_income'] }} </p>
    <p><strong>Saldo final: </strong>S/. {{$data['cash_final_balance']}} </p>
    <p><strong>Egreso: </strong>S/. {{$data['cash_egress']}} </p>
    <p><strong>Por cobrar: </strong>S/. {{$data['credit']}} </p>
</div>
@if($data['cash_documents_total']>0)
    <div class="" style="width:100% !important">
        <div class=" ">
            <table>
                <thead>
                <tr width="100%">
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">#</th>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Descripcion</th>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Suma</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['methods_payment'] as $item)
                    <tr>
                        <td class="celda">
                            {{ $item['iteracion'] }}
                        </td>
                        <td class="celda">
                            {{ $item['name'] }}
                        </td>
                        <td class="celda">
                            {{ $item['sum'] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
            <table class="">
                <thead>
                <tr>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">#</th>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Transacción</th>
                    {{-- <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">T. Doc</th> --}}
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Documento</th>
                    {{-- <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Fecha emisión</th> --}}
                    {{-- <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Cliente/Proveedor</th>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">N° Documento</th> --}}
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Moneda</th>
                    <th style="font-weight: bold;background: #0088cc;color: white;text-align: center;">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data['all_documents'] as $key => $value)
                    <tr>
                        <td class="celda">
                            {{ $loop->iteration }}{{--
                            <br> {!! $value['usado'] !!}  <br> <strong>{{$value['tipo']}}</strong> --}}
                        </td>
                        <td class="celda">
                            {{ $value['type_transaction'] }}
                        </td>
                        {{--<td class="celda">
                            {{ $value['document_type_description'] }}
                        </td>--}}
                        <td class="celda">
                            {{ $value['number'] }}
                        </td>
                        {{--<td class="celda">
                            {{ $value['date_of_issue'] }}
                        </td>
                        <td class="celda">
                            {{ $value['customer_name'] }}
                        </td>
                        <td class="celda">
                            {{ $value['customer_number'] }}
                        </td>--}}
                        <td class="celda">
                            {{ $value['currency_type_id'] }}
                        </td>
                        <td class="celda">
                            {{ $value['total_string'] }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="callout callout-info">
        <p>No se encontraron registros.</p>
    </div>
@endif
</body>
</html>
