<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>RH</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Hoteles</strong></h3>
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
                                <th >Nombres y Apellidos</th>
                                <th>Tipo doc. Indetidad</th>
                                <th>Número</th>
                                <th>Sexo</th>
                                <th >Ocupación</th>
                                <th >Edad</th>
                                <th >E. Civil</th>
                                <th >Nacionalidad</th>
                                <th >Procedencia</th>
                                <th>N° Habitación</th>
                                <th>F. Ingreso</th>
                                <th>H .Ingreso</th>
                                <th>F. Salida</th>
                                <th>H .Salida</th>
                                <th >Comprobante</th> 
                                <th >Total</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->identity_document_type->description}}</td>
                                <td>{{$value->number}}</td>
                                <td>{{$value->sex}}</td>
                                <td>{{$value->ocupation}}</td>
                                <td >{{ $value->age }}</td>
                                <td >{{ $value->civil_status }}</td>
                                <td >{{ $value->nacionality }}</td>
                                <td>{{ $value->origin}}</td>
                                <td>{{ $value->room_number}}</td>
                                <td>{{ $value->date_entry}}</td>
                                <td>{{ $value->time_entry}}</td>
                                <td>{{ $value->date_exit}}</td>
                                <td>{{ $value->time_exit}}</td>
                                <td>{{ $value->document->number_full}}</td>
                                <td>{{ $value->document->total}}</td>
                        
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
