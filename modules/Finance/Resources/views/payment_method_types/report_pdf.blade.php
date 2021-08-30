<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Ingresos y Egresos - Métodos de pago</title>
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
            <p align="center" class="title"><strong>Ingresos y Egresos - Métodos de pago</strong></p>
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
            <div class="callout callout-info">
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
