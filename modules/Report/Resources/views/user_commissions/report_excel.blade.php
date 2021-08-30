<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Comisión vendores</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte de comisión de vendedores - utilidades</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                <tr>
                    <td>
                        <p><b>Empresa: </b></p>
                    </td>
                    <td align="center">
                        <p><strong>{{$company->name}}</strong></p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong></p>
                    </td>
                    <td align="center">
                        <p><strong>{{date('Y-m-d')}}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong></p>
                    </td>
                    <td align="center">{{$company->number}}</td>
                  
                </tr>
            </table>
        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" "> 
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendedor</th>
                                <th class="text-center">Tipo comisión</th>
                                <th class="text-center">Monto comisión</th>
                                <th class="text-center">Total utilidad</th>
                                <th class="text-center">Total comisiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $row)
                                @php

                                    $utilities = Modules\Report\Helpers\UserCommissionHelper::getUtilities($row->sale_notes, $row->documents);
                                    $commission = Modules\Report\Helpers\UserCommissionHelper::getCommission($row, $utilities);

                                @endphp
                                
                                <tr>
                                    <td class="celda" >{{$loop->iteration}}</td>
                                    <td class="celda">{{$row->name}}</td>
                                    <td class="celda">{{($row->user_commission->type == 'amount') ? 'Monto':'Porcentaje'}}</td>
                                    <td class="celda">{{$row->user_commission->amount}}</td> 
                                    <td class="celda">{{$utilities['total_utility']}}</td> 
                                    <td class="celda">{{$commission}}</td> 
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
