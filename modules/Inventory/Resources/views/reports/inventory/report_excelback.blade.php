<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Inventario</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Inventario</strong></h3>
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
                    <td>
                        <p><strong>Establecimiento: </strong></p>
                    </td>
                    <td align="center">{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
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
                                <th>Cod. Interno</th>
                                <th>Descripción</th>
                                <th>Categoria</th>
                                <th>Inventario actual</th>
                                <th>Costo</th>
                                <th>Costo Total</th>
                                <th>Precio de venta</th>
                                <td>Marca</td>
                                <td>F. vencimiento</td>
                                <th>Almacén</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($records as $key => $value)
                                @php
                                    $total_line = $value->stock * $value->item->purchase_unit_price;
                                    $total = $total + $total_line;
                                @endphp
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$value->item->internal_id ?? ''}}</td>
                                    <td>{{$value->item->description ?? ''}}</td>
                                    <td >{{optional($value->item->category)->name}}</td>
                                    <td>{{$value->stock}}</td>
                                    <td>{{$value->item->purchase_unit_price}}</td>
                                    <td>{{number_format($total_line, 6)}}</td>
                                    <td>{{$value->item->sale_unit_price}}</td>
                                    <td>{{ $value->item->brand->name }}</td>
                                    <td>{{ $value->item->date_of_due }}</td>
                                    <td>{{$value->warehouse->description}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" style="text-align: right;">Costo Total de Inventario</td>
                                <td>{{number_format($total, 6)}}</td>
                                <td></td>
                                <td></td>
                            </tr>
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
