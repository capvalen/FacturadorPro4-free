<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Cotizaciones</title>
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
            <p align="center" class="title"><strong>Reporte Cotizaciones</strong></p>
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
        @if(!empty($reports))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Fecha Emisión</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Cotización</th>

                                <th>Comprobantes</th>
                                <th>Notas de venta</th>
                                <th class="text-center">Moneda</th>
                                <th class="text-right">T.Exportación</th>
                                <th class="text-right" >T.Inafecta</th>

                                <th class="text-right">T.Exonerado</th>
                                <th class="text-right">T.Gravado</th>
                                <th class="text-right">T.Igv</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                                    <td class="celda">{{$value->customer->name}}</td>
                                    <td class="celda">{{$value->state_type->description}}</td>
                                    <td class="celda">{{$value->identifier}}</td>
                                    <td class="celda">
                                        @foreach ($value->documents as $doc)
                                             <label class="d-block">{{$doc->number_full}}</label>
                                        @endforeach
                                    </td>
                                    <td class="celda">
                                        @foreach ($value->sale_notes as $doc)
                                             <label class="d-block">{{$doc->identifier}}</label>
                                        @endforeach
                                    </td>
                                    
                                    <td class="celda">{{$value->currency_type_id}}</td>
                                    <td class="celda">{{$value->total_exportation}}</td>
                                    <td class="celda">{{$value->total_unaffected}}</td>
                                    <td class="celda">{{ $value->total_exonerated}}</td>
                                    <td class="celda">{{ $value->total_taxed}}</td>
                                    <td class="celda">{{ $value->total_igv}}</td>
                                    <td class="celda">{{ $value->total}}</td>
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
