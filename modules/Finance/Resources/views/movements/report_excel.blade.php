<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Movimientos de ingresos y egresos</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Movimientos de ingresos y egresos</strong></h3>
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
                                <th class="">Fecha</th>
                                <th class="">Adquiriente</th>
                                <th class="">N° Doc. Identidad</th>
                                <th class="">Tipo documento</th>
                                <th class="">Documento/Transacción</th>
                                <th class="">Detalle</th>
                                <th class="">Moneda</th>
                                <th class="">Tipo</th>
                                <th class="">Ingresos</th>
                                <th class="">Gastos</th>
                                <th class="">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $balance = 0;
                                $final_balance = 0;
                                $total_input = 0;
                                $total_output = 0;
                            @endphp
                            @foreach($records as $key => $value)
                                <tr>
                                    @php 
                                        $data_person = $value->data_person;
                                        $document_type = '';
                                        $items = [];

                                        if($value->payment->associated_record_payment->document_type){

                                            $document_type = $value->payment->associated_record_payment->document_type->description;
                                        
                                        }elseif(isset($value->payment->associated_record_payment->prefix)){
                                            
                                            $document_type = $value->payment->associated_record_payment->prefix;

                                        }

                                        $payment_method_type_description = '';

                                        if($value->payment->payment_method_type){
                                            $payment_method_type_description = $value->payment->payment_method_type->description;
                                        }else{
                                            $payment_method_type_description = $value->payment->expense_method_type->description;
                                        }
                                        $balance =  ($value->type_movement == 'input') ?  $balance + $value->payment->payment : $balance - $value->payment->payment;

                                        $total_input += ($value->type_movement == 'input') ? $value->payment->payment : 0;
                                        $total_output += ($value->type_movement == 'output') ? $value->payment->payment : 0;

                                        if(in_array($value->instance_type, ['expense', 'income'])){

                                            $items = $value->payment->associated_record_payment->items->transform(function($row, $key) {
                                                return [
                                                    'description' => $row->description 
                                                ];
                                            });
                                        }

                                    @endphp
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value->payment->date_of_payment->format('Y-m-d')}}</td> 
                                    <td class="celda">{{$data_person->name}}</td>
                                    <td class="celda">{{$data_person->number}}</td>
                                    <td class="celda">{{ $document_type }}</td>
                                    <td class="celda">{{$value->payment->associated_record_payment->number_full}}</td>
                                    <td class="celda">
                                        @foreach ($items as $item)
                                            <p>- {{ $item['description'] }}</p>
                                        @endforeach
                                    </td>
                                    <td class="celda">{{$value->payment->associated_record_payment->currency_type_id}}</td>
                                    <td class="celda">{{$value->instance_type_description}}</td>

                                    <td class="celda"> {{ ($value->type_movement == 'input') ? "S/".number_format($value->payment->payment, 2, ".", "") : '-' }}</td>
                                    <td class="celda"> {{ ($value->type_movement == 'output') ? "S/".number_format($value->payment->payment, 2, ".", "") : '-' }}</td>
                                    <td class="celda">S/{{ $balance }}</td>
                                </tr>

                                 
                            @endforeach 
                        </tbody>            
                        <tfoot>
                            <tr>
                                <td colspan="9" class="celda"></td>
                                <td class="celda">S/{{$total_input}}</td>
                                <td class="celda">S/{{$total_output}}</td>
                                <td class="celda">S/{{$total_input - $total_output}}</td>
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
