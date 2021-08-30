<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kardex valorizado</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Kardex valorizado</strong></h3>
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
                                <th>Producto</th>
                                <th >Categoría</th>
                                <th >Marca</th>
                                <th class="text-center">Unidad</th>
                                <th class="text-center">Unidades físicas vendidas</th>
                                <th class="text-center">Costo unitario</th>
                                <th class="text-center">Valor de ventas</th>
                                <th class="text-center">Costo de producto</th>
                                <th class="text-center">Unidad valorizada</th>
                                <th >Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{ $value['item_description'] }}</td>
                                <td>{{ $value['category_description'] }}</td>
                                <td>{{ $value['brand_description'] }}</td>
                                <td  class="text-center">{{ $value['unit_type_id'] }}</td>
                                <td  class="text-center">{{ $value['quantity_sale'] }}</td>
                                <td class="text-center">{{ $value['purchase_unit_price'] }}</td>
                                <td class="text-center">{{ $value['total_sales'] }}</td>
                                <td class="text-center">{{ $value['item_cost'] }}</td>
                                <td class="text-center">{{ $value['valued_unit'] }}</td>
                                <td>
                                    @foreach($value['warehouses'] as $item)
                                    <span>{{$item['description']}}</span><br>
                                    @endforeach
                                </td>

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
