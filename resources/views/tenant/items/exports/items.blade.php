@php
    $max_prices_columns = \App\Models\Tenant\ItemUnitType::select(\DB::raw('count(item_id) as total'))
    ->wherein('item_id',$records->pluck('id'))
    ->groupby('item_id')
    ->get()->max('total');
    @endphp<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Productos</title>
</head>
<body>
<div>
    <h3 align="center" class="title"><strong>Reporte Productos</strong></h3>

</div>
<br>
@if(!empty($records))
    <div class="">
        <div class=" ">
            <table class="">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Código interno</th>
                    <th>Nombre</th>
                    <th>Nombre alternativo</th>
                    <th>Descripción</th>
                    <th>Modelo</th>
                    <th>Unidad de medida</th>
                    <th>Posee IGV</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Precio</th>
                    <th>Fecha de vencimiento</th>
                    @for($i=0;$i<$max_prices_columns;$i++)
                        <th>Unidad</th>
                        <th>Descripcion</th>
                        <th>Factor</th>
                        <th>Precio 1</th>
                        <th>Precio 2</th>
                        <th>Precio 3</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($records as $key => $value)
                    @php
                        $item_unit_types = $value->item_unit_types->toArray();
                    @endphp
                    <tr>
                        <td class="celda">{{$loop->iteration}}</td>
                        <td class="celda">{{$value->internal_id}}</td>
                        <td class="celda">{{$value->name}}</td>
                        <td class="celda">{{$value->second_name }}</td>
                        <td class="celda">{{$value->description }}</td>
                        <td class="celda">{{$value->model }}</td>
                        <td class="celda">{{$value->unit_type_id }}</td>
                        <td class="celda">{{$value->has_igv }}</td>
                        <td class="celda">{{$value->category_id }}</td>
                        <td class="celda">{{$value->brand_id }}</td>
                        <td class="celda">{{$value->sale_unit_price }}</td>
                        <td class="celda">{{$value->date_of_due }}</td>
                        @for($i=0;$i<$max_prices_columns;$i++)
                            @php
                                $unidad = '';
                                $descripcion = '';
                                $factor = '';
                                $precio_1 = '';
                                $precio_2 = '';
                                $precio_3 = '';
                                if(isset($item_unit_types[$i])){
                                    $temp = $item_unit_types[$i];
                                    $unidad = $temp['unit_type_id'];
                                    $descripcion = $temp['description'];
                                    $factor = $temp['quantity_unit'];
                                    $precio_1 = $temp['price1'];
                                    $precio_2 = $temp['price2'];
                                    $precio_3 = $temp['price3'];
                                }
                            @endphp
                            <td>{{$unidad}}</td>
                            <td>{{$descripcion}}</td>
                            <td>{{$factor}}</td>
                            <td>{{$precio_1}}</td>
                            <td>{{$precio_2}}</td>
                            <td>{{$precio_3}}</td>
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
