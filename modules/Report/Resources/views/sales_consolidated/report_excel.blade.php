<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Consolidado de items</title>
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
            <p align="center" class="title"><strong>Consolidado de items por cliente/vendedor</strong></p>
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
                    @inject('reportService', 'Modules\Report\Services\ReportService')
                    {{-- @if($params['seller_id'])
                    <td>
                        <p><strong>Usuario: </strong>{{$reportService->getUserName($params['seller_id'])}}</p>
                    </td>
                    @endif  --}}
                    @if($params['sellers'])
                        @php
                            $sellers = json_decode($params['sellers']);
                        @endphp
                        @if(count($sellers) > 0)
                        <td>
                            <p><strong>Usuario(s): </strong>
                            @foreach ($sellers as $seller_id)
                            - {{$reportService->getUserName($seller_id)}}
                            @endforeach
                            </p>
                        </td>
                        @endif 
                    @endif


                    @if($params['person_id'])
                    <td>
                        <p><strong>Cliente: </strong>{{$reportService->getPersonName($params['person_id'])}}</p>
                    </td>
                    @endif 
                </tr>
            </table>
        </div>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    @php 
                        $acum_total=0; 
                    @endphp
                    <table class="">
                        <thead>
                            <tr>
                                <th class="">#</th>
                                <th  class="text-center">Documento</th>
                                <th  class="text-center">Cod. Interno</th>
                                <th  class="text-center">Unidad</th>
                                <th  class="text-left">Producto</th>
                                <th  class="text-center">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->series}}-{{$value->number}}</td> 
                                    <td class="celda">{{$value->relation_item->internal_id}}</td>
                                    <td class="celda">{{ ($value->item->presentation) ? $value->item->presentation->unit_type_id : $value->relation_item->unit_type_id}}</td>
                                    {{-- <td class="celda">{{$value->relation_item->unit_type_id}}</td> --}}
                                    <td class="celda">{{$value->item->description}}</td>
                                    <td class="celda">{{$value->quantity}}</td> 
                                </tr> 

                                @php
                                    $acum_total += $value->quantity;
                                @endphp
                            @endforeach
                            <tr>
                                <td class="celda" colspan="4"></td>
                                <td class="celda" ><strong>Total</strong></td>
                                <td class="celda">{{$acum_total}}</td>
                            </tr> 
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
