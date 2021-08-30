<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Series</title>
    </head>
    <body>  
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serie</th>
                                <th>Producto</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Vendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)

                            @php
                                
                                $status = '';

                                if($value->has_sale){
                                    $status = 'SI';
                                }else{
                                    $status = 'NO';
                                }

                            @endphp 

                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->series}}</td>
                                <td class="celda">{{$value->item->description}}</td>
                                <td class="celda">{{$value->date}}</td>
                                <td class="celda">{{$value->state}}</td>
                                <td class="celda">{{$status}}</td>
 
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
