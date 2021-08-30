<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Inventario</title>
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
                font-size: 10px;
                border: 0.1px solid black;
            }

            th {
                font-size: 10px;
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
            <p align="center" class="title"><strong>Reporte Inventario</strong></p>
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
                    <td>
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
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
                                <th>Descripción</th>
                                <th>Categoria</th>
                                <th>Inventario actual</th>
                                <th>Costo</th>
                                <th>Costo Total</th>
                                <th>Precio de venta</th>
                                <th>Marca</th>
                                <th>F. vencimiento</th>
                                <th>Almacén</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($reports as $key => $value)
                                @php
                                    $total_line = $value->stock * $value->item->purchase_unit_price;
                                    $total = $total + $total_line;
                                @endphp
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->item->description ?? ''}}</td>
                                    <td class="celda">{{optional($value->item->category)->name}}</td>
                                    <td class="celda">{{$value->stock}}</td>
                                    <td class="celda">{{$value->item->purchase_unit_price}}</td>
                                    <td class="celda">{{number_format($total_line, 6)}}</td>
                                    <td class="celda">{{$value->item->sale_unit_price}}</td>
                                    <td class="celda">{{$value->item->brand->name}}</td>
                                    <td class="celda">{{$value->item->date_of_due}}</td>
                                    <td class="celda">{{$value->warehouse->description}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="celda" colspan="5" style="text-align: right;">Costo Total de Inventario</td>
                                <td class="celda">{{number_format($total, 6)}}</td>
                                <td class="celda"></td>
                                <td class="celda"></td>
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
