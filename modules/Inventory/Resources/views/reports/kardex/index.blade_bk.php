@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div>
                        <h4 class="card-title">Consulta kardex</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{route('reports.kardex.search')}}" class="el-form demo-form-inline el-form--inline" method="GET">
                            <div class="box">
                                <div class="box-body no-padding">

                                   <!-- {{Form::label('item_id', 'Producto')}}-->
                                    <!--{{Form::select('item_id', $items->pluck('description', 'id'), old('item_id', request()->item_id), ['class' => 'form-control col-md-6'])}} -->
                                    <tenant-product
                                        :data_d="{{json_encode(isset($_GET['d']) ? $_GET['d']:null)}}"
                                        :data_a="{{json_encode(isset($_GET['a']) ? $_GET['a']:null)}}"
                                        :data_products="{{json_encode($items)}}"
                                        :item_selected="{{json_encode(isset($_GET['item_selected']) ? $_GET['item_selected']:null)}}"

                                    ></tenant-product>
                                </div>
                                <div class="el-form-item col-xs-12">
                                    <div class="el-form-item__content">
                                        <button class="btn btn-custom" type="submit"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(!empty($reports) && $reports->count())
                    <div class="box">
                        <div class="box-body no-padding">
                            <div style="margin-bottom: 10px">
                                @if(isset($reports))
                                    <form action="{{route('reports.kardex.pdf')}}" class="d-inline" method="POST">
                                        {{csrf_field()}}

                                        <input type="hidden" value="{{$d}}" name="d">
                                        <input type="hidden" value="{{$a}}" name="a">
                                        <input type="hidden" name="item_id" value="{{$_GET['item_selected']}}">
                                        <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-pdf"></i> Exportar PDF</button>
                                    </form>
                                <form action="{{route('reports.kardex.report_excel')}}" class="d-inline" method="POST">
                                    {{csrf_field()}}

                                    <input type="hidden" value="{{$d}}" name="d">
                                    <input type="hidden" value="{{$a}}" name="a">
                                    <input type="hidden" name="item_id" value="{{$_GET['item_selected']}}">
                                    <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-excel"></i> Exportar Excel</button>
                                </form>
                                @endif
                            </div>
                            <table width="100%" class="table table-striped table-responsive-xl table-bordered table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha y hora transacción</th>
                                        <th>Tipo transacción</th>
                                        <th>Número</th>
                                        <th>Feha emisión</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $key => $value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->created_at}}</td>
                                        <td>

                                            @switch($value->inventory_kardexable_type)
                                                @case($models[0])
                                                    {{($value->quantity < 0) ? "Venta":"Anulación Venta"}}
                                                    @break
                                                @case($models[1])
                                                    {{($value->quantity < 0) ? "Anulación Compra":"Compra"}}
                                                    @break

                                                @case($models[2])
                                                    {{"Nota de venta"}}
                                                    @break

                                                @case($models[3])
                                                    {{$value->inventory_kardexable->description}}
                                                    @break
                                            @endswitch


                                        </td>
                                        <td>
                                            @switch($value->inventory_kardexable_type)
                                                @case($models[0])
                                                    {{ optional($value->inventory_kardexable)->series.'-'.optional($value->inventory_kardexable)->number }}
                                                    @break
                                                @case($models[1])
                                                    {{ optional($value->inventory_kardexable)->series.'-'.optional($value->inventory_kardexable)->number }}
                                                    @break
                                                @case($models[2])
                                                    {{ optional($value->inventory_kardexable)->prefix.'-'.optional($value->inventory_kardexable)->id }}
                                                    @break
                                                @case($models[3])
                                                    {{"-"}}
                                                    @break
                                            @endswitch

                                        </td>

                                        <td>

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
                                            @endswitch



                                        </td>

                                        @php
                                        if($value->inventory_kardexable_type == $models[3]){
                                            if(!$value->inventory_kardexable->type){
                                                $transaction = Modules\Inventory\Models\InventoryTransaction::findOrFail($value->inventory_kardexable->inventory_transaction_id);
                                            }
                                        }
                                        @endphp


                                        <td>
                                            @switch($value->inventory_kardexable_type)

                                                @case($models[0])
                                                    {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                    @break

                                                @case($models[1])
                                                    {{ ($value->quantity > 0) ?  $value->quantity:"-"}}
                                                    @break

                                                @case($models[3])
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{ ($value->inventory_kardexable->type == 1) ? $value->quantity : "-" }}

                                                    @else

                                                        {{($transaction->type == 'input') ? $value->quantity : "-" }}

                                                    @endif
                                                    @break

                                                @default
                                                    {{"-"}}
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($value->inventory_kardexable_type)
                                                @case($models[0])
                                                    {{ ($value->quantity < 0) ?  $value->quantity:"-" }}
                                                    @break
                                                @case($models[1])
                                                    {{ ($value->quantity < 0) ?  $value->quantity:"-"}}
                                                    @break
                                                @case($models[2])
                                                    {{  $value->quantity }}
                                                    @break
                                                @case($models[3])
                                                    @if($value->inventory_kardexable->type != null)

                                                        {{($value->inventory_kardexable->type == 2 || $value->inventory_kardexable->type == 3) ? $value->quantity : "-" }}

                                                    @else

                                                        {{($transaction->type == 'output') ? $value->quantity : "-" }}

                                                    @endif
                                                    @break
                                                @default
                                                    {{"-"}}
                                                    @break
                                            @endswitch

                                        </td>
                                        @php
                                            $balance += $value->quantity;
                                        @endphp
                                        <td>{{number_format($balance, 4)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            Total {{$reports->total()}}
                            <label class="pagination-wrapper ml-2">
                                {{$reports->appends($_GET)->render()}}
                            </label>
                        </div>
                    </div>
                    @else
                    <div class="box box-body no-padding">
                        <strong>No se encontraron registros</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
