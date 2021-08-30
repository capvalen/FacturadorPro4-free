<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>REPORTE PRODUCTOS</title>
    </head>
    <body>
        @if(!empty($records))
            <div>
                <div class=" ">
                    <table>
                        <thead>
                            <tr>
                                <th>FECHA DE EMISIÓN</th>
                                <th>TIPO DOCUMENTO</th>
                                <th>ID TIPO</th>
                                <th>SERIE</th>
                                <th>NÚMERO</th>
                                <th>ANULADO</th>
                                <th>DOC ENTIDAD TIPO DNI RUC</th>
                                <th>DOC ENTIDAD NÚMERO</th>
                                <th>DENOMINACIÓN ENTIDAD</th>
                                <th>MONEDA</th>
                                <th>TIPO DE CAMBIO</th>
                                <th>UNIDAD DE MEDIDA</th>
                                <th>CÓDIGO INTERNO</th>
                                <th>DESCRIPCIÓN</th>
                                <th>CANTIDAD</th>
                                <th>SERIES</th>
                                <th>COSTO UNIDAD</th>
                                <th>VALOR UNITARIO</th>
                                <th>PRECIO UNITARIO</th>
                                <th>DESCUENTO</th>
                                <th>SUBTOTAL</th>
                                <th>TIPO DE IGV</th>
                                <th>IGV</th>
                                <th>TIPO DE ISC</th>
                                <th>ISC</th>
                                <th>IMPUESTO BOLSAS</th>
                                <th>TOTAL</th>
                                @if($type == 'sale')
                                <th>TOTAL COMPRA</th>
                                <th>GANANCIA</th>
                                @endif
                                <th>PLATAFORMA</th>
                                <th>MARCA</th>
                                <th>CATEGORÍA</th>
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

                                            // $purchase_unit_price = 0;

                                            // if($value->relation_item->purchase_unit_price > 0){
                                            //     $purchase_unit_price = $value->relation_item->purchase_unit_price;
                                            // }else{
                                            //     $purchase_item = \App\Models\Tenant\PurchaseItem::select('unit_price')->where('item_id', $value->item_id)->latest('id')->first();
                                            //     $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : $value->unit_price;
                                            // }

                                            $total_item_purchase = \Modules\Report\Http\Resources\GeneralItemCollection::getPurchaseUnitPrice($value);
                                            // $total_item_purchase = $purchase_unit_price * $value->quantity;
                                            $utility_item = $value->total - $total_item_purchase;

                                        @endphp
                                        <tr>
                                            <td class="celda">{{$value->sale_note->date_of_issue->format('Y-m-d')}}</td>
                                            <td class="celda">NOTA DE VENTA</td>
                                            <td class="celda">80</td>
                                            <td class="celda">{{$value->sale_note->series}}</td>
                                            <td class="celda">{{$value->sale_note->number}}</td>
                                            <td class="celda">{{$value->sale_note->state_type_id == '11' ? 'SI':'NO'}}</td>
                                            <td class="celda">{{$value->sale_note->customer->identity_document_type->description}}</td>
                                            <td class="celda">{{$value->sale_note->customer->number}}</td>
                                            <td class="celda">{{$value->sale_note->customer->name}}</td>
                                            <td class="celda">{{$value->sale_note->currency_type_id}}</td>
                                            <td class="celda">{{$value->sale_note->exchange_rate_sale}}</td>
                                            <td class="celda">{{$value->sale_note->unit_type_id}}</td>
                                            <td class="celda">{{$value->relation_item->brand->name}}</td>
                                            <td class="celda">{{$value->item->description}}</td>
                                            <td class="celda">{{$value->quantity}}</td>

                                            <td class="celda">{{$series}}</td>

                                            <td class="celda">{{($value->relation_item) ? $value->relation_item->purchase_unit_price:0}}</td>

                                            <td class="celda">{{$value->unit_value}}</td>
                                            <td class="celda">{{$value->unit_price}}</td>

                                            <td class="celda">{{$value->total_discount}}</td>

                                            <td class="celda">{{$value->total_value}}</td>
                                            <td class="celda">{{$value->affectation_igv_type_id}}</td>
                                            <td class="celda">{{$value->total_igv}}</td>
                                            <td class="celda">{{$value->system_isc_type_id}}</td>
                                            <td class="celda">{{$value->total_isc}}</td>
                                            <td class="celda">{{$value->total_plastic_bag_taxes}}</td>

                                            <td class="celda">{{$value->total}}</td>

                                            <td class="celda">{{ number_format($total_item_purchase,2) }}</td>
                                            <td class="celda">{{ number_format($utility_item ,2) }}</td>

                                            <td class="celda">{{ optional($value->relation_item->web_platform)->name }}</td>
                                            <td class="celda">{{$value->relation_item->brand->name}}</td>
                                            <td class="celda">{{$value->relation_item->category->name}}</td>
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

                                            // $purchase_unit_price = 0;

                                            // if($value->relation_item->purchase_unit_price > 0){
                                            //     $purchase_unit_price = $value->relation_item->purchase_unit_price;
                                            // }else{
                                            //     $purchase_item = \App\Models\Tenant\PurchaseItem::select('unit_price')->where('item_id', $value->item_id)->latest('id')->first();
                                            //     $purchase_unit_price = ($purchase_item) ? $purchase_item->unit_price : $value->unit_price;
                                            // }

                                            $total_item_purchase = \Modules\Report\Http\Resources\GeneralItemCollection::getPurchaseUnitPrice($value);
                                            $utility_item = $value->total - $total_item_purchase;
                                        @endphp

                                    <tr>
                                        <td class="celda">{{$value->document->date_of_issue->format('Y-m-d')}}</td>
                                        <td class="celda">{{$value->document->document_type->description}}</td>
                                        <td class="celda">{{$value->document->document_type_id}}</td>
                                        <td class="celda">{{$value->document->series}}</td>
                                        <td class="celda">{{$value->document->number}}</td>
                                        <td class="celda">{{$value->document->state_type_id == '11' ? 'SI':'NO'}}</td>
                                        <td class="celda">{{$value->document->customer->identity_document_type->description}}</td>
                                        <td class="celda">{{$value->document->customer->number}}</td>
                                        <td class="celda">{{$value->document->customer->name}}</td>
                                        <td class="celda">{{$value->document->currency_type_id}}</td>
                                        <td class="celda">{{$value->document->exchange_rate_sale}}</td>
                                        <td class="celda">{{$value->item->unit_type_id}}</td>
                                        <td class="celda">{{$value->item->internal_id}}</td>
                                        <td class="celda">{{$value->item->description}}</td>
                                        <td class="celda">{{$value->quantity}}</td>

                                        <td class="celda">{{$series}}</td>


                                        <td class="celda">{{($value->relation_item) ? $value->relation_item->purchase_unit_price:0}}</td>

                                        <td class="celda">{{$value->unit_value}}</td>
                                        <td class="celda">{{$value->unit_price}}</td>

                                        <td class="celda">{{$value->total_discount}}</td>

                                        <td class="celda">{{$value->total_value}}</td>
                                        <td class="celda">{{$value->affectation_igv_type_id}}</td>
                                        <td class="celda">{{$value->total_igv}}</td>
                                        <td class="celda">{{$value->system_isc_type_id}}</td>
                                        <td class="celda">{{$value->total_isc}}</td>
                                        <td class="celda">{{$value->total_plastic_bag_taxes}}</td>

                                        <td class="celda">{{$value->total}}</td>

                                        <td class="celda">{{ number_format($total_item_purchase,2) }}</td>
                                        <td class="celda">{{ number_format($utility_item ,2) }}</td>

                                        <td class="celda">{{ optional($value->relation_item->web_platform)->name }}</td>
                                        <td class="celda">{{$value->relation_item->brand->name}}</td>
                                        <td class="celda">{{$value->relation_item->category->name}}</td>
                                    </tr>
                                    @endforeach

                                @endif


                            @else

                                @foreach($records as $key => $value)
                                <tr>
                                    <td class="celda">{{$value->purchase->date_of_issue->format('Y-m-d')}}</td>
                                    <td class="celda">{{$value->purchase->document_type->description}}</td>
                                    <td class="celda">{{$value->purchase->document_type_id}}</td>
                                    <td class="celda">{{$value->purchase->series}}</td>
                                    <td class="celda">{{$value->purchase->number}}</td>
                                    <td class="celda">{{$value->purchase->state_type_id == '11' ? 'SI':'NO'}}</td>
                                    <td class="celda">{{$value->purchase->supplier->identity_document_type->description}}</td>
                                    <td class="celda">{{$value->purchase->supplier->number}}</td>
                                    <td class="celda">{{$value->purchase->supplier->name}}</td>
                                    <td class="celda">{{$value->purchase->currency_type_id}}</td>
                                    <td class="celda">{{$value->purchase->exchange_rate_sale}}</td>
                                    <td class="celda">{{$value->item->unit_type_id}}</td>

                                    <td class="celda">{{$value->relation_item ? $value->relation_item->internal_id:''}}</td>

                                    <td class="celda">{{$value->item->description}}</td>
                                    <td class="celda">{{$value->quantity}}</td>

                                    <td class="celda"></td>
                                    <td class="celda"></td>

                                    <td class="celda">{{$value->unit_value}}</td>
                                    <td class="celda">{{$value->unit_price}}</td>

                                    <td class="celda">
                                    @if($value->discounts)
                                        {{collect($value->discounts)->sum('amount')}}
                                    @endif
                                    </td>

                                    <td class="celda">{{$value->total_value}}</td>
                                    <td class="celda">{{$value->affectation_igv_type_id}}</td>
                                    <td class="celda">{{$value->total_igv}}</td>
                                    <td class="celda">{{$value->system_isc_type_id}}</td>
                                    <td class="celda">{{$value->total_isc}}</td>
                                    <td class="celda">{{$value->total_plastic_bag_taxes}}</td>

                                    <td class="celda">{{$value->total}}</td>
                                    <td class="celda"></td>

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
