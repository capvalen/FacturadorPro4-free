<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte de Cuentas por pagar</title>
</head>
<body>
    <div>
            <h3 align="center" class="title"><strong>Reporte de Cuentas por pagar</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                <tr>
                    <td>
                        <p><b>Empresa: </b></p>
                    </td>
                @foreach($companies as $value)
            
                    <td align="center">
                        <p><strong>{{$value->name}}</strong></p>
                    </td>
        
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong></p>
                    </td>
                    <td align="center">{{$value->number}}</td>
                
                @endforeach
        
                    <td>
                        <p><strong>Fecha: </strong></p>
                    </td>
                    <td align="center">
                        <p><strong>{{date('Y-m-d')}}</strong></p>
                    </td>
                </tr>
            </table>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Fecha Emisión</th>
                                <th>Número</th>

                                <th>Cliente</th>
                                <th>Por pagar</th>
                                <th>Total</th>


                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                                @if($value['total_to_pay'] > 0)
                                    <tr>
                                        <td class="celda">{{$loop->iteration}}</td>
                                        <td class="celda">{{$value['date_of_issue']}}</td>
                                        <td class="celda">{{$value['number_full']}}</td>

                                        <td class="celda">{{$value['supplier_name']}}</td>
                                        <td class="celda">{{$value['total_to_pay']}}</td>
                                        <td class="celda">{{$value['total']}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
</html>
