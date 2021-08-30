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
                        <form action="{{route('tenant.reports.kardex.search')}}" class="el-form demo-form-inline el-form--inline" method="POST">
                            {{csrf_field()}}
                            <div class="box ">
                                <div class="box-body no-padding">
                                    {{Form::label('item_id', 'Producto')}}
                                    {{Form::select('item_id', $items->pluck('description', 'id'), old('item_id', request()->item_id), ['class' => 'form-control col-md-6'])}}
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
                                    <form action="{{route('tenant.report.kardex.pdf')}}" class="d-inline" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="item_id" value="{{old('item_id', request()->item_id)}}">
                                        <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-pdf"></i> Exportar PDF</button>
                                        {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                    </form>
                                <form action="{{route('tenant.report.kardex.report_excel')}}" class="d-inline" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" name="item_id" value="{{old('item_id', request()->item_id)}}">
                                    <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-excel"></i> Exportar Excel</button>
                                    {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                </form>
                                @endif
                            </div>
                            <table width="100%" class="table table-striped table-responsive-xl table-bordered table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha y hora</th>
                                        <th>Tipo transacción</th>
                                        <th>Número</th>
                                        <th>Entrada</th>
                                        <th>Salida</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $key => $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td>{{$value->created_at}}</td>
                                        <td>
                                            @switch($value->type)
                                                @case('sale')
                                                    {{($value->sale_note_id) ? "Nota de Venta" : (($value->quantity >= 0) ? "Venta" : "Anulación")}}
                                                    @break
                                                @case('purchase')
                                                    {{"Compra"}}
                                                    @break
                                                @default
                                                    {{"Stock Inicial"}}
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($value->type)
                                                @case('sale')
                                                    {{($value->document_id) ? "{$value->document->series}-{$value->document->number}" : "{$value->sale_note->prefix}-{$value->sale_note->id}"}}
                                                    @break
                                                @case('purchase')
                                                    {{"{$value->purchase->series}-{$value->purchase->number}"}}
                                                    @break
                                                @default
                                                    {{"-"}}
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{($value->type == 'purchase' || !$value->type) ? number_format($value->quantity, 4) : number_format(0, 4)}}</td>
                                        <td>{{($value->type == 'sale') ? number_format($value->quantity, 4) : number_format(0, 4)}}</td>
                                        @php
                                            if ($value->type == 'purchase' || !$value->type) $balance += $value->quantity;
                                            if ($value->type == 'sale') $balance -= $value->quantity;
                                        @endphp
                                        <td>{{number_format($balance, 4)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper">
                                {{-- {{ $reports->appends(['search' => Session::get('form_document_list')])->render()  }} --}}
                                {{-- {{$reports->links()}} --}}
                            </div>
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
