<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Pagos</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Pagos</strong></h3>

        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ruc</th>
                                <th>Fecha</th>
                                <th>Factura</th>
                                <th class="">Nombre Comercial</th>

                                <th>Razon Social</th>
                                <th>Zona</th>
                                <th>Tipo de cliente</th>
                                <th>Departamento</th>
                                <th>Distrito</th>
                                <th class="">Total Factura</th>

                                @for ($i = 1; $i <= $payment_count; $i++)
                                     <th>Pago {{$i}}</th>
                                @endfor
                             
                                <th>Saldo</th>

                                @for ($i = 1; $i <= $payment_count; $i++)
                                     <th>Referencia {{$i}}</th>
                                @endfor

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->ruc}}</td>
                                <td class="celda"> 
                                    {{$value->date}}
                                </td>
                                <td class="celda">{{$value->invoice }}</td>
                                <td class="celda">{{$value->comercial_name }}</td>


                                <td class="celda">{{$value->business_name }}</td>
                                <td class="celda">{{$value->zone }}</td>
                                <td class="celda">{{$value->person_type }}</td>
                                <td class="celda">{{$value->department }}</td>
                                <td class="celda">{{$value->district }}</td>
                                <td class="celda">{{$value->total }}</td>

                                @for ($i = 0; $i < $payment_count; $i++)
                                    <td class="celda">{{  ( isset($value->payments[$i]) ) ?  number_format($value->payments[$i]->payment, 2, ".", "") : '' }}</td>
                                @endfor

                                <td  class="celda">{{$value->balance }} </td>

                                @for ($i = 0; $i < $payment_count; $i++)
                                    <td class="celda">{{ ( isset($value->payments[$i]) ) ?  $value->payments[$i]->reference : '' }}</td>
                                @endfor


                            </tr>
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
    </body>
</html>
