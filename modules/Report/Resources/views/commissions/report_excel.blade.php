<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Comisión vendores</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte de comisión de vendedores</strong></h3>
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
            <div>
                <p>No se encontraron registros.</p>
            </div>
        @endif
    </body>
</html>
