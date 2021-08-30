<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>M. Pago</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>M. Pago</strong></h3>
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
                                <th class="">Método de pago / Total pagos</th>
                                <th class="">CPE</th>
                                <th class="">NV</th>
                                <th class="">COT</th>
                                <th class="">Contrato</th>
                                <th class="">S. Técnico</th>
                                <th class="">Ingresos</th>
                                <th class="">Compras</th>
                                <th class="">Gastos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records['records'] as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    <td class="celda">{{$value['description']}}</td>
                                    <td class="celda">{{$value['document_payment']}}</td>
                                    <td class="celda">{{$value['sale_note_payment']}}</td>
                                    <td class="celda">{{$value['quotation_payment']}}</td>
                                    <td class="celda">{{$value['contract_payment']}}</td>
                                    <td class="celda">{{$value['technical_service_payment']}}</td>
                                    <td class="celda">{{$value['income_payment']}}</td>
                                    <td class="celda"> {{$value['purchase_payment']}}</td>
                                    <td class="celda">{{$value['expense_payment']}}</td>
                                </tr>
                            @endforeach
                            <tr> 
                                <td class="celda" colspan="2">Totales</td>
                                <td class="celda">{{$records['totals']['t_documents']}}</td>
                                <td class="celda">{{$records['totals']['t_sale_notes']}}</td>
                                <td class="celda">{{$records['totals']['t_quotations']}}</td>
                                <td class="celda">{{$records['totals']['t_contracts']}}</td>
                                <td class="celda">{{$records['totals']['t_technical_services']}}</td>
                                <td class="celda">{{$records['totals']['t_income']}}</td>
                                <td class="celda"> {{$records['totals']['t_purchases']}}</td>
                                <td class="celda">{{$records['totals']['t_expenses']}}</td>
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
