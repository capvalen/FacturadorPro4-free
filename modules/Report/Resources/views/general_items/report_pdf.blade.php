<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>REPORTE PRODUCTOS</title>
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
                font-size: 9px;
            }

            th {
                padding: 5px;
                font-size: 9px;
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
            @page {
                margin: 6px;
            }
        </style>
    </head>
    <body>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="" style="table-layout:fixed;">
                        <thead>
                            <tr width="100%">
                                <th style="width:6%;">FECHA DE EMISIÓN</th>
                                <th style="width:4%;">SERIE</th>
                                <th style="width:4%;">NÚMERO</th>
                                <th style="width:6%;">DOC ENTIDAD TIPO DNI RUC</th>
                                <th style="width:8%;">DOC ENTIDAD NÚMERO</th>
                                <th style="width:11%;">DENOMINACIÓN ENTIDAD</th>
                                <th style="width:4%;">MONEDA</th>
                                <th style="width:5%;">UNIDAD DE MEDIDA</th>
                                <th style="width:5%;">MARCA</th>
                                <th style="width:12%;">DESCRIPCIÓN</th>
                                <th style="width:5%;">CATEGORÍA</th>
                                <th style="width:5%;">CANTIDAD</th>
                                <th style="width:5%;">PRECIO UNITARIO</th>
                                <th style="width:5%;">TOTAL</th>
                                @if($type == 'sale')
                                <th style="width:6%;">TOTAL COMPRA</th>
                                <th style="width:7%;">GANANCIA</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($type == 'sale')

                                @if($document_type_id == '80')

                                    @foreach($records as $key => $value)

                                        @php
                                            $series = '';
                                            if(isset($value->item->lots) )
                                            {
                                                $series_data =  collect($value->item->lots)->where('has_sale', 1)->pluck('series')->toArray();
                                                $series = implode(" - ", $series_data);
                                            }

                                            $total_item_purchase = \Modules\Report\Http\Resources\GeneralItemCollection::getPurchaseUnitPrice($value);
                                            $utility_item = $value->total - $total_item_purchase;

                                        @endphp
                                        <tr>
                                            <td class="celda">{{$value->sale_note->date_of_issue->format('Y-m-d')}}</td>
                                            <td class="celda">{{$value->sale_note->series}}</td>
                                            <td class="celda">{{$value->sale_note->number}}</td>
                                            <td class="celda">{{$value->sale_note->customer->identity_document_type_id}}</td>
                                            <td class="celda">{{$value->sale_note->customer->number}}</td>
                                            <td class="celda">{{$value->sale_note->customer->name}}</td>
                                            <td class="celda">{{$value->sale_note->currency_type_id}}</td>
                                            <td class="celda">{{$value->item->unit_type_id}}</td>
                                            <td class="celda">{{$value->relation_item->brand->name}}</td>
                                            <td class="celda">{{$value->item->description}}</td>
                                            <td class="celda">{{$value->relation_item->category->name}}</td>
                                            <td class="celda">{{number_format($value->quantity, 2)}}</td>
                                            <td class="celda">{{number_format($value->unit_price, 2)}}</td>
                                            <td class="celda">{{number_format($value->total, 2)}}</td>
                                            <td class="celda">{{number_format($total_item_purchase,2)}}</td>
                                            <td class="celda">{{number_format( $utility_item,2) }}</td>
                                        </tr>
                                    @endforeach

                                @else

                                    @foreach($records as $key => $value)

                                        @php
                                            $series = '';
                                            if(isset($value->item->lots) )
                                            {
                                                $series_data =  collect($value->item->lots)->where('has_sale', 1)->pluck('series')->toArray();
                                                $series = implode(" - ", $series_data);
                                            }

                                            $total_item_purchase = \Modules\Report\Http\Resources\GeneralItemCollection::getPurchaseUnitPrice($value);

                                            $utility_item = $value->total - $total_item_purchase;
                                        @endphp

                                    <tr>

                                        <td class="celda">{{$value->document->date_of_issue->format('Y-m-d')}}</td>
                                        <td class="celda">{{$value->document->series}}</td>
                                        <td class="celda">{{$value->document->number}}</td>
                                        <td class="celda">{{$value->document->customer->identity_document_type_id}}</td>
                                        <td class="celda">{{$value->document->customer->number}}</td>
                                        <td class="celda">{{$value->document->customer->name}}</td>
                                        <td class="celda">{{$value->document->currency_type_id}}</td>
                                        <td class="celda">{{$value->item->unit_type_id}}</td>
                                        <td class="celda">{{$value->relation_item->brand->name}}</td>
                                        <td  class="celda" >{{ (strlen($value->item->description) > 50) ? substr($value->item->description,0,50):$value->item->description}}</td>
                                        <td class="celda">{{$value->relation_item->category->name}}</td>
                                        <td class="celda">{{number_format($value->quantity, 2)}}</td>
                                        <td class="celda">{{number_format($value->unit_price, 2)}}</td>
                                        <td class="celda">{{number_format($value->total, 2)}}</td>
                                        <td class="celda">{{ number_format($total_item_purchase,2) }}</td>
                                        <td class="celda">{{ number_format($utility_item ,2) }}</td>
                                    </tr>
                                    @endforeach

                                @endif


                            @else

                                @foreach($records as $key => $value)
                                <tr>
                                    <td class="celda">{{$value->purchase->date_of_issue->format('Y-m-d')}}</td>
                                    {{-- <td class="celda">{{$value->purchase->document_type->description}}</td> --}}
                                    {{-- <td class="celda">{{$value->purchase->document_type_id}}</td> --}}
                                    <td class="celda">{{$value->purchase->series}}</td>
                                    <td class="celda">{{$value->purchase->number}}</td>
                                    {{-- <td class="celda">{{$value->purchase->state_type_id == '11' ? 'SI':'NO'}}</td> --}}
                                    <td class="celda">{{$value->purchase->supplier->identity_document_type_id}}</td>
                                    <td class="celda">{{$value->purchase->supplier->number}}</td>
                                    <td class="celda">{{$value->purchase->supplier->name}}</td>
                                    <td class="celda">{{$value->purchase->currency_type_id}}</td>
                                    {{-- <td class="celda">{{$value->purchase->exchange_rate_sale}}</td> --}}
                                    <td class="celda">{{$value->item->unit_type_id}}</td>

                                    {{-- <td class="celda">{{$value->relation_item ? $value->relation_item->internal_id:''}}</td> --}}
                                    <td class="celda">{{$value->relation_item->brand->name}}</td>
                                    <td class="celda">{{$value->item->description}}</td>
                                    <td class="celda">{{$value->relation_item->category->name}}</td>
                                    <td class="celda">{{number_format($value->quantity, 2)}}</td>
                                    <td class="celda">{{number_format($value->unit_price, 2)}}</td>
                                    <td class="celda">{{number_format($value->total, 2)}}</td>
                                    {{-- <td class="celda"></td> --}}

                                </tr>
                                @endforeach
                            @endif
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
