<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Pagos</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte pagos</strong></h3>
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
                                <th class="">#</th>
                                <th class="">Adquiriente</th>
                                <th class="">N° Doc. Identidad</th>
                                <th class="">Tipo documento</th>
                                <th class="">Documento/Transacción</th>
                                <th class="">Moneda</th>
                                <th class="">Tipo</th>
                                <th class="">Destino</th>
                                <th class="">F. Pago</th>
                                <th class="">Método</th>
                                <th class="">Referencia</th>
                                <th class="">Responsable</th>
                                <th class="">Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                                <tr>
                                    @php 
                                        $data_person = $value->data_person;
                                        $document_type = '';

                                        if($value->payment->associated_record_payment->document_type){

                                            $document_type = $value->payment->associated_record_payment->document_type->description;
                                        
                                        }elseif($value->instance_type == 'technical_service'){
                
                                            $document_type = 'ST';
                                            
                                        }elseif(isset($value->payment->associated_record_payment->prefix)){
                                            
                                            $document_type = $value->payment->associated_record_payment->prefix;

                                        }

                                        $payment_method_type_description = '';

                                        if($value->payment->payment_method_type){
                                            $payment_method_type_description = $value->payment->payment_method_type->description;
                                        }else{
                                            $payment_method_type_description = $value->payment->expense_method_type->description;
                                        }

                                    @endphp
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$data_person->name}}</td>
                                    <td class="celda">{{$data_person->number}}</td>
                                    <td class="celda">{{ $document_type }}</td>
                                    <td class="celda">{{$value->payment->associated_record_payment->number_full}}</td>
                                    <td class="celda">{{$value->payment->associated_record_payment->currency_type_id}}</td>
                                    <td class="celda">{{$value->instance_type_description}}</td>
                                    <td class="celda">{{$value->destination_description}}</td>
                                    <td class="celda">{{$value->payment->date_of_payment->format('Y-m-d')}}</td> 
                                    <td class="celda">{{$payment_method_type_description}}</td>  
                                    <td class="celda">{{$value->payment->reference}}</td>
                                    <td class="celda">{{ optional($value->user)->name }}</td>
                                    <td class="celda">{{$value->payment->payment}}</td>
                                </tr>

                                 
                            @endforeach 
                        </tbody>
                        <tfoot> 
                            <tr>
                                <td class="celda" colspan="11"></td>
                                <td class="celda"><strong>Totales PEN</strong></td> 
                                <td class="celda">{{ $records->where('payment.associated_record_payment.currency_type_id', 'PEN')->sum('payment.payment') }}</td>
                            </tr>
                            <tr>
                                <td colspan="11"></td>
                                <td class="celda"><strong>Totales USD</strong></td> 
                                <td class="celda">{{ $records->where('payment.associated_record_payment.currency_type_id', 'USD')->sum('payment.payment') }}</td>

                            </tr>
                        </tfoot>
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
