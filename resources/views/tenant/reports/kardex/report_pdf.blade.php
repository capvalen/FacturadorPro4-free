<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Kardex</title>
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
            <p align="center" class="title"><strong>Reporte Kardex</strong></p>
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
                                <th>Fecha y hora</th>
                                <th>Tipo transacción</th>
                                <th>Número</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $key => $value)
                                <tr>
                                    <td class="celda">{{$value->id}}</td>
                                    <td class="celda">{{$value->created_at}}</td>
                                    <td class="celda">
                                        @switch($value->type)
                                            @case('sale') 
                                                {{($value->sale_note_id) ? "Nota de Venta" : (($value->quantity >= 0) ? "Venta" : "Anulación")}}
                                                @break
                                            @case('purchase')
                                                {{"Compra"}}                                                    
                                                @break
                                            @default                                                    
                                                {{"Stock Inicial"}}                                                    
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="celda">
                                        @switch($value->type)
                                            @case('sale')
                                                {{($value->document_id) ? "{$value->document->series}-{$value->document->number}" : "{$value->sale_note->prefix}-{$value->sale_note->id}"}}
                                                @break
                                            @case('purchase')
                                                {{"{$value->purchase->series}-{$value->purchase->number}"}}                                                    
                                                @break
                                            @default                                                    
                                                {{"-"}}                                                    
                                            @break
                                        @endswitch
                                    </td>    
                                    <td class="celda">{{($value->type == 'purchase' || !$value->type) ? number_format($value->quantity, 4) : number_format(0, 4)}}</td>
                                    <td class="celda">{{($value->type == 'sale') ? number_format($value->quantity, 4) : number_format(0, 4)}}</td>
                                    @php
                                        if ($value->type == 'purchase' || !$value->type) $balance += $value->quantity;
                                        if ($value->type == 'sale') $balance -= $value->quantity;
                                    @endphp
                                    <td class="celda">{{number_format($balance, 4)}}</td>
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
