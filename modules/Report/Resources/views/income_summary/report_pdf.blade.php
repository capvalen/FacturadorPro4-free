@php

$establishment = $cash->user->establishment;
 
$cash_documents = $cash->cash_documents;
 
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte Resúmen de ingresos - {{$cash->user->name}} - {{$cash->date_opening}} {{$cash->time_opening}}</title>
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
                font-size: 12px;
            }
            
            thead {
                font-weight: bold;
                background: #0088cc;
                color: white;
                text-align: center;
            }
            .td-custom { line-height: 0.1em; }
        </style>
    </head>
    <body>
        <div>
            <p align="center" class="title"><strong>Resúmen de ingresos por métodos de pago</strong></p>
        </div>
        <div style="margin-top:20px; margin-bottom:20px;">
            <table> 
                <tr>
                    <td class="td-custom">
                        <p><strong>Empresa: </strong>{{$company->name}}</p>
                    </td>
                    <td class="td-custom">
                        <p><strong>Fecha reporte: </strong>{{date('Y-m-d')}}</p>
                    </td>
                </tr>
                <tr>
                    <td class="td-custom">
                        <p><strong>Ruc: </strong>{{$company->number}}</p>
                    </td>
                    <td class="td-custom">
                        <p><strong>Establecimiento: </strong>{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</p>
                    </td>
                </tr>
                
                <tr>
                    <td class="td-custom">
                        <p><strong>Vendedor: </strong>{{$cash->user->name}}</p>
                    </td>
                    <td class="td-custom">
                        <p><strong>Fecha y hora apertura: </strong>{{$cash->date_opening}} {{$cash->time_opening}}</p>
                    </td>
                </tr>
                <tr>
                    <td class="td-custom">
                        <p><strong>Estado de caja: </strong>{{($cash->state) ? 'Aperturada':'Cerrada'}}</p>
                    </td>
                    @if(!$cash->state)
                    <td class="td-custom">
                        <p><strong>Fecha y hora cierre: </strong>{{$cash->date_closed}} {{$cash->time_closed}}</p>
                    </td>
                    @endif
                </tr> 
            </table> 
        </div>
        @if($cash_documents->count())
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha y hora emisión</th>
                                <th>Tipo documento</th>
                                <th>Documento</th>
                                <th>Método de pago</th> 
                                <th>Moneda</th>
                                <th>Importe</th>
                                <th>Vuelto</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cash_documents as $value)
                            
                                @php
                                    
                                    $type_transaction =  null;
                                    $document_type_description = null;
                                    $number = null;
                                    $date_time_of_issue = null;
                                    $payment_method_description = null;
                                    $total = null;  
                                    $currency_type_id = null;

                                @endphp

                                @if($value->sale_note)
 
                                    @foreach($value->sale_note->payments as $payment)
                                    <tr>
                                        @php
                                            $type_transaction =  'Venta';
                                            $document_type_description =  'NOTA DE VENTA';
                                            $number = $value->sale_note->identifier;
                                            $date_time_of_issue = "{$value->sale_note->date_of_issue->format('Y-m-d')} {$value->sale_note->time_of_issue}";
                                            $payment_method_description = $payment->payment_method_type->description;
                                            $total = $payment->payment;
                                            
                                            if(!in_array($payment->associated_record_payment->state_type_id, ['01','03','05','07','13'])){
                                                $total = 0;
                                            }

                                            $currency_type_id = $value->sale_note->currency_type_id;

                                        @endphp


                                        <td class="celda">{{ $loop->iteration }}</td>
                                        <td class="celda">{{ $date_time_of_issue}}</td>
                                        <td class="celda">{{ $document_type_description }}</td>
                                        <td class="celda">{{ $number }}</td>
                                        <td class="celda">{{$payment_method_description }}</td>  
                                        <td class="celda">{{$currency_type_id }}</td>  
                                        <td class="celda">{{ number_format($value->sale_note->total,2) }}</td>
                                        <td class="celda">{{ number_format($payment->change,2) }}</td>
                                        <td class="celda">{{ number_format($total,2) }}</td>

                                    </tr>
                                    @endforeach

                                @elseif($value->document)
                                 
                                    @foreach($value->document->payments as $payment)
                                    <tr>
                                        @php
                                            $type_transaction =  'Venta';
                                            $document_type_description =  $value->document->document_type->description;
                                            $number = $value->document->number_full;
                                            $date_time_of_issue = "{$value->document->date_of_issue->format('Y-m-d')} {$value->document->time_of_issue}";
                                            $payment_method_description = $payment->payment_method_type->description;
                                            $total = $payment->payment;
                                            
                                            if(!in_array($payment->associated_record_payment->state_type_id, ['01','03','05','07','13'])){
                                                $total = 0;
                                            }
                                            
                                            $currency_type_id = $value->document->currency_type_id;

                                        @endphp


                                        <td class="celda">{{ $loop->iteration }}</td>
                                        <td class="celda">{{ $date_time_of_issue}}</td>
                                        <td class="celda">{{ $document_type_description }}</td>
                                        <td class="celda">{{ $number }}</td>
                                        <td class="celda">{{$payment_method_description }}</td>  
                                        <td class="celda">{{$currency_type_id }}</td>  
                                        <td class="celda">{{ number_format($value->document->total,2) }}</td>
                                        <td class="celda">{{ number_format($payment->change,2) }}</td>
                                        <td class="celda">{{ number_format($total,2) }}</td>

                                    </tr>
                                    @endforeach
                                @endif
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
