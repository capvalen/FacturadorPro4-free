<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Clientes</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>{{ ($type == 'customers') ? 'Reporte Clientes':'Reporte Proveedores' }}</strong></h3>

        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo de documento</th>
                                <th>Número de documento</th>
                                <th>Nombre</th>
                                <th>Nombre comercial</th>
                                <th>Código interno</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->identity_document_type->description}}</td>
                                <td class="celda">{{$value->number}}</td>
                                <td class="celda">{{$value->name }}</td>
                                <td class="celda">{{$value->trade_name }}</td>
                                <td class="celda">{{$value->internal_code }}</td>
                                <td class="celda">{{$value->email }}</td>
                                <td class="celda">{{$value->telephone }}</td>
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
