<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Nota de ventas</title>
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
            <p align="center" class="title"><strong>Reporte Nota de Venta</strong></p>
        </div>
        <div style="margin-top:20px; margin-bottom:20px;">
            <table>
                <tr>
                    <td>
                        <p><strong>Empresa: </strong>{{$company->name}}</p>
                    </td>
                    <td>
                        <p><strong>Fecha: </strong>{{date('Y-m-d')}}</p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><strong>Ruc: </strong>{{$company->number}}</p>
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
                               <th>#</th>
                                <th class="text-center">Fecha Emisión</th>
                                <th>Cliente</th>
                                <th>Nota de Venta</th>
                                <th>Estado</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-right" >T.Exportación</th>
                                <th class="text-right" >T.Inafecta</th>
                                <th class="text-right" >T.Exonerado</th>
                                <th class="text-right">T.Gravado</th>
                                <th class="text-right">T.Igv</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Comprobantes</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$value->date_of_issue->format('Y-m-d')}}</td>
                                    <td>{{$value->customer->name}}</td>
                                    <td>{{$value->identifier}}</td>
                                    <td>{{$value->state_type->description}}</td>
                                    <td>{{$value->currency_type_id}}</td>
                                    <td >{{ $value->total_exportation }}</td>
                                    <td >{{ $value->total_unaffected }}</td>
                                    <td >{{ $value->total_exonerated }}</td>
                                    <td>{{ $value->total_taxed}}</td>
                                    <td>{{ $value->total_igv}}</td>
                                    <td>{{ $value->total}}</td>
                                    <td>
                                        @foreach ($value->documents as $doc)
                                            <label class="d-block">{{$doc->number_full}}</label>
                                        @endforeach
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
