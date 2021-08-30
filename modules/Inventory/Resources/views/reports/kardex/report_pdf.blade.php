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
                <tr>
                    <td>
                        <p><strong>Producto: </strong>{{$item->internal_id ? $item->internal_id.' - '.$item->description : $item->description}}</p>
                    </td>
                    <td>
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
                                @if(!$item_id)
                                <th>Producto</th>
                                @endif
                                <th>Fecha y hora transacción</th>
                                <th>Tipo transacción</th>
                                <th>Número</th>
                                <th>NV. Asociada</th>
                                <th>Pedido</th>
                                <th>CPE. Asociado</th>
                                <th>Feha emisión</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                @if($item_id)
                                <th>Saldo</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $key => $value)
                                <tr>
                                    <td class="celda">{{$loop->iteration}}</td>
                                    @if(!$item_id)
                                        <td class="celda">{{$value->item->description}}</td>
                                    @endif
                                    <td class="celda">{{$value->created_at}}</td>
                                    <td class="celda">

                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])
                                                {{($value->quantity < 0) ? "Venta":"Anulación"}}
                                                @break
                                            @case($models[1])
                                                {{"Compra"}}
                                                @break

                                            @case($models[2])
                                                {{"Nota de venta"}}
                                                @break

                                            @case($models[3])
                                                {{$value->inventory_kardexable->description}}
                                                @break

                                            @case($models[4])
                                                {{($value->quantity < 0) ? "Pedido":"Anulación pedido"}}
                                                @break

                                            @case($models[5])
                                                {{"Devolución"}}
                                                @break
                                        @endswitch


                                    </td>
                                    <td class="celda">
                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])
                                                {{ optional($value->inventory_kardexable)->series."-".optional($value->inventory_kardexable)->number }}
                                                @break
                                            @case($models[1])
                                                {{optional($value->inventory_kardexable)->series."-".optional($value->inventory_kardexable)->number}}
                                                @break

                                            @case($models[2])
                                                {{  optional($value->inventory_kardexable)->number_full }}
                                                {{-- {{  optional($value->inventory_kardexable)->prefix."-".optional($value->inventory_kardexable)->id }}                                                     --}}
                                                @break

                                            @case($models[3])
                                                {{"-"}}
                                                @break

                                            @case($models[4])
                                                {{  optional($value->inventory_kardexable)->prefix."-".optional($value->inventory_kardexable)->id }}
                                                @break

                                            @case($models[5])
                                                {{  optional($value->inventory_kardexable)->number_full }}
                                                @break
                                        @endswitch

                                    </td>
                                    <td class="celda">
                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])

                                                {{ isset($value->inventory_kardexable->sale_note_id)  ? optional($value->inventory_kardexable)->sale_note->number_full:"-" }}
                                                @break
                                            @default
                                                {{"-"}}
                                                @break
                                        @endswitch

                                    </td>

                                    <td class="celda">
                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])

                                                {{ isset($value->inventory_kardexable->order_note_id)  ? optional($value->inventory_kardexable)->order_note->number_full:"-" }}
                                                @break
                                            @default
                                                {{"-"}}
                                                @break
                                        @endswitch

                                    </td>

                                    <td class="celda">

                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])
                                                {{ isset($value->inventory_kardexable->note) ? $value->inventory_kardexable->note->affected_document->getNumberFullAttribute() : '' }}
                                                @break
                                            @case($models[1])
                                                {{'-'}}
                                                @break
                                            @case($models[2])
                                                {{"-"}}
                                                @break
                                            @case($models[3])
                                                {{"-"}}
                                                @break
                                            @case($models[4])
                                                {{"-"}}
                                                @break
                                            @case($models[5])
                                                {{"-"}}
                                                @break
                                        @endswitch

                                    </td>

                                    <td class="celda">

                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])
                                                {{ isset($value->inventory_kardexable->date_of_issue) ? $value->inventory_kardexable->date_of_issue->format('Y-m-d') : '' }}
                                                @break
                                            @case($models[1])
                                                {{ isset($value->inventory_kardexable->date_of_issue) ? $value->inventory_kardexable->date_of_issue->format('Y-m-d') : '' }}
                                                @break
                                            @case($models[2])
                                                {{ isset($value->inventory_kardexable->date_of_issue) ? $value->inventory_kardexable->date_of_issue->format('Y-m-d') : '' }}
                                                @break
                                            @case($models[3])
                                                {{"-"}}
                                                @break
                                            @case($models[4])
                                                {{ isset($value->inventory_kardexable->date_of_issue) ? $value->inventory_kardexable->date_of_issue->format('Y-m-d') : '' }}
                                                @break
                                            @case($models[5])
                                                {{ isset($value->inventory_kardexable->date_of_issue) ? $value->inventory_kardexable->date_of_issue->format('Y-m-d') : '' }}
                                                @break
                                        @endswitch



                                    </td>
                                    @php
                                        if($value->inventory_kardexable_type == $models[3]){
                                            if(!$value->inventory_kardexable->type){
                                                $transaction = Modules\Inventory\Models\InventoryTransaction::findOrFail($value->inventory_kardexable->inventory_transaction_id);
                                            }
                                        }
                                    @endphp
                                    <td class="celda">
                                        @switch($value->inventory_kardexable_type)

                                            @case($models[0])
                                                {{ ($value->quantity > 0) ?  ( isset($value->inventory_kardexable->sale_note_id) || isset($value->inventory_kardexable->order_note_id)  ? "-":$value->quantity):"-"}}
                                                @php
                                                    if( isset($value->inventory_kardexable->sale_note_id) || isset($value->inventory_kardexable->order_note_id)){
                                                        $value->quantity = 0;
                                                    }
                                                @endphp
                                                @break
                                            @case($models[1])
                                                {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                @break

                                            @case($models[2])
                                                {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                @break

                                            @case($models[3])
                                                @if ($userWarehouse === $value->inventory_kardexable->warehouse_destination_id)
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{ ($value->inventory_kardexable->type == 1) ? "-" : $value->quantity }}

                                                    @else

                                                        {{($transaction->type == 'output') ? $value->quantity : "-" }}

                                                    @endif
                                                @else
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{ ($value->inventory_kardexable->type == 1) ? $value->quantity : "-" }}

                                                    @else

                                                        {{($transaction->type == 'input') ? $value->quantity : "-" }}

                                                    @endif
                                                @endif
                                                @break

                                            @case($models[4])
                                                {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                @break

                                            @case($models[5])
                                                {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                @break

                                            @default
                                                {{"-"}}
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="celda">

                                        @switch($value->inventory_kardexable_type)
                                            @case($models[0])
                                                {{ ($value->quantity < 0) ?  ( isset($value->inventory_kardexable->sale_note_id) || isset($value->inventory_kardexable->order_note_id)  ? "-":$value->quantity):"-" }}

                                                @php
                                                ($value->quantity < 0) ?  ( isset($value->inventory_kardexable->sale_note_id) || isset($value->inventory_kardexable->order_note_id) ? $value->quantity = 0:$value->quantity):"-";
                                                @endphp
                                                @break

                                            @case($models[1])
                                                {{ ($value->quantity < 0) ?  $value->quantity:"-"}}
                                                @break

                                            @case($models[2])
                                                {{  ($value->quantity < 0) ?  $value->quantity:"-" }}
                                                @break

                                            @case($models[3])
                                                @if ($userWarehouse === $value->inventory_kardexable->warehouse_destination_id)
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{($value->inventory_kardexable->type == 2 || $value->inventory_kardexable->type == 3) ? "-" : $value->quantity }}

                                                    @else

                                                        {{($transaction->type == 'input') ? $value->quantity : "-" }}

                                                    @endif
                                                @else
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{($value->inventory_kardexable->type == 2 || $value->inventory_kardexable->type == 3) ? $value->quantity : "-" }}

                                                    @else

                                                        {{($transaction->type == 'output') ? $value->quantity : "-" }}

                                                    @endif
                                                @endif
                                                @break

                                            @case($models[4])
                                                {{ ($value->quantity < 0) ?  $value->quantity:"-"}}
                                                @break

                                            @case($models[5])
                                                {{  ($value->quantity < 0) ?  $value->quantity:"-" }}
                                                @break

                                            @default
                                                {{"-"}}
                                                @break
                                        @endswitch

                                    </td>
                                    @php
                                        $balance += $value->quantity;
                                    @endphp

                                    @if($item_id)
                                        <td class="celda">{{number_format($balance, 4)}}</td>
                                    @endif
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
