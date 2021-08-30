@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div>
                        <h4 class="card-title">Consulta de Cotizaciones</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{route('tenant.reports.quotations.search')}}" class="el-form demo-form-inline el-form--inline" method="GET">
                            <tenant-calendar-quotation  data_d="{{$d ?? ''}}"   data_a="{{$a ?? ''}}"></tenant-calendar-quotation>
                        </form>
                    </div>
                    @if(!empty($reports) && $reports->count())
                    <div class="box">
                        <div class="box-body no-padding">
                            <div style="margin-bottom: 10px">
                                @if(isset($reports))
                                    <form action="{{route('tenant.reports.quotations.pdf')}}" class="d-inline" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" value="{{$d}}" name="d">
                                        <input type="hidden" value="{{$a}}" name="a">
                                    
                                 
                                        <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-pdf"></i> Exportar PDF</button>
                                        {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                    </form>
                                <form action="{{route('tenant.report.quotations.report_excel')}}" class="d-inline" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" value="{{$d}}" name="d">
                                    <input type="hidden" value="{{$a}}" name="a">
                                
                                    <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-excel"></i> Exportar Excel</button>
                                    {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                </form>
                                @endif
                            </div>
                            <table width="100%" class="table table-striped table-responsive-xl table-bordered table-hover">
                                <thead class="">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Fecha Emisión</th>
                                        <th>Cliente</th>
                                        <th>Estado</th>
                                        <th>Cotización</th>

                                        <th>Comprobantes</th>
                                        <th>Notas de venta</th>
                                        <th class="text-center">Moneda</th>
                                        <th class="text-right">T.Exportación</th>
                                        <th class="text-right" >T.Inafecta</th>

                                        <th class="text-right">T.Exonerado</th>
                                        <th class="text-right">T.Gravado</th>
                                        <th class="text-right">T.Igv</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $key => $value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->date_of_issue->format('Y-m-d')}}</td>
                                        <td>{{$value->customer->name}}</td>
                                        <td>{{$value->state_type->description}}</td>
                                        <td>{{$value->identifier}}</td>
                                        <td>
                                            @foreach ($value->documents as $doc)
                                                 <label class="d-block">{{$doc->number_full}}</label>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($value->sale_notes as $doc)
                                                 <label class="d-block">{{$doc->identifier}}</label>
                                            @endforeach
                                        </td>

                                        
                                        <td>{{$value->currency_type_id}}</td>
                                        <td>{{$value->total_exportation}}</td>
                                        <td>{{$value->total_unaffected}}</td>

                                        <td>{{ $value->total_exonerated}}</td>
                                        <td>{{ $value->total_taxed}}</td>
                                        <td>{{ $value->total_igv}}</td>
                                        <td>{{ $value->total}}</td>
                                      
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            Total {{$reports->total()}}
                            <label class="pagination-wrapper ml-2">
                                {{-- {{ $reports->appends(['search' => Session::get('form_document_list')])->render()  }} --}}
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
