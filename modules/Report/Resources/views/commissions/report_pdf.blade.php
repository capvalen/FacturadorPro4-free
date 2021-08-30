<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Comisiones vendedores</title>
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
            <p align="center" class="title"><strong>Reporte de comisión de vendedores</strong></p>
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
                
                </tr>
            </table>
        </div>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vendedor</th>
                                <th class="text-center">Cantidad transacciones</th>
                                <th class="text-center">Ventas acumuladas</th>
                                <th class="text-center">Total comisiones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $row)
                                @php
                                
                                    $total_commision = 0;
                                    $total_commision_document = 0;
                                    $total_commision_sale_note = 0;

                                    $total_transactions_document = $row->documents->count();
                                    $total_transactions_sale_note = $row->sale_notes->count();
                                    $total_transactions = $total_transactions_document + $total_transactions_sale_note;

                                    $acum_sales_document = $row->documents->sum('total');
                                    $acum_sales_sale_note = $row->sale_notes->sum('total');
                                    $acum_sales = $acum_sales_document + $acum_sales_sale_note;


                                    foreach ($row->documents as $document) {
                                        // $total_commision_document += $document->items->sum('relation_item.commission_amount'); 
                                        foreach ($document->items as $item) {
                                            if ($item->relation_item->commission_amount) {

                                                if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                                                    $total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                                                }
                                                else{

                                                    $total_commision_document += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                                                    
                                                }
                                                
                                                //$total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                                            }
                                        } 

                                    }

                                    foreach ($row->sale_notes as $sale_note) {
                                        // $total_commision_sale_note += $sale_note->items->sum('relation_item.commission_amount'); 
                                        foreach ($sale_note->items as $item) {
                                            if ($item->relation_item->commission_amount) {
                                                
                                                if(!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount'){

                                                    $total_commision_sale_note += $item->quantity * $item->relation_item->commission_amount;
                                                }
                                                else{

                                                    $total_commision_sale_note += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount/100);
                                                    
                                                }
                                                //$total_commision_sale_note += ($item->quantity * $item->relation_item->commission_amount);
                                            }
                                        }
                                    }

                                    $total_commision = $total_commision_document + $total_commision_sale_note;
                                @endphp
                                
                                <tr>
                                    <td class="celda" >{{$loop->iteration}}</td>
                                    <td class="celda">{{$row->name}}</td>
                                    <td class="celda">{{$total_transactions}}</td>
                                    <td class="celda">{{$acum_sales}}</td> 
                                    <td class="celda">{{$total_commision}}</td> 
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
