<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte General de Pedidos</title>
        <style>
            html {
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
                border-color: #0088cc;
                border: 0.1px solid black;
            }
            
            .title {
                font-weight: bold;
                padding: 5px;
                font-size: 20px !important;
                text-decoration: underline;
            }
            
            p>strong {
                margin-left: 5px;
                font-size: 13px;
            }
            
            thead {
                font-weight: bold;
                background: #0088cc;
                color: white;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div>
            <p align="center" class="title"><strong>Reporte General de Pedidos por cliente/vendedor</strong></p>
        </div>
        <div style="margin-top:20px; margin-bottom:20px;">
            <table>
                <tr>
                    <td>
                        <p><strong>Empresa: </strong>{{$company->name}}</p>
                    </td>
                    <td>
                        <p><strong>Ruc: </strong>{{$company->number}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
                    </td>
                    <td>
                        @if($params['person_id'])
                            <p><strong>Cliente: </strong>{{ (!empty($records)) ? $records->first()->customer->name:'' }} - {{ (!empty($records)) ? $records->first()->customer->number:'' }}</p>
                        @else
                            <p><strong>Vendedor: </strong>{{ (!empty($records)) ? $records->first()->user->name:'' }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th class="">#</th>
                                <th  class="celda">F. Emisión</th>
                                <th  class="celda">F. Entrega</th>
                                <th  class="celda">N° Pedido</th>
                                @if($params['person_id'])
                                    <th  class="celda" >Vendedor</th>
                                @else
                                    <th  class="celda" >Cliente</th>
                                    <th  class="celda" >N° Doc. Identidad</th>
                                @endif
                                <th  class="celda">Monto</th>
                                <th  class="celda">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td  class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                                    <td  class="celda">{{optional($value->delivery_date)->format('Y-m-d')}}</td>
                                    <td  class="celda">{{$value->number_full}}</td>
                                    @if($params['person_id'])
                                        <td  class="celda" >{{$value->user->name}}</td>
                                    @else
                                        <td class="celda" >{{ $value->customer->name }}</td>
                                        <td class="celda" >{{ $value->customer->number }}</td>
                                    @endif
                                    <td  class="celda">{{$value->total}}</td>
                                    <td  class="celda">{{($value->documents->count() > 0) ? 'PROCESADO' : 'PENDIENTE'}}</td>
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
