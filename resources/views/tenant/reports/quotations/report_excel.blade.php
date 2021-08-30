<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Compras</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Cotizacion</strong></h3>
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
                            @foreach($records as $key => $value)
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
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
