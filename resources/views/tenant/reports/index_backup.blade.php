@extends('tenant.layouts.app')


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                   <div>
                <h4 class="card-title">Consulta de Documentos</h4>
                </div></div>
            <div class="card-body">

                @if (Session::has('form_document_list'))
                {{ Form::model(Session::get('form_document_list'), ['role' => 'form', 'autocomplete' => 'off', 'method' => 'POST']) }}
                @else
                {{ Form::open(['role' => 'form', 'autocomplete' => 'off', 'method' => 'POST']) }}
                @endif
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('document_type', 'Tipo de Documento') }}
                                {{ Form::select('document_type', ['' => 'Todos', '01'=> 'Factura', '03' => 'Boleta', '07' => 'Nota de Credito', '08' => 'Nota de Debito'], null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {{ Form::label('serie', 'Serie') }}
                                {{ Form::text('serie', null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {{ Form::label('number', 'Correlativo') }}
                                {{ Form::text('number', null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {{ Form::label('total', 'Importe Total') }}
                                {{ Form::text('total', null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('state', 'Estado') }}
                                {{ Form::select('state', $states, 0, array('class' => 'form-control')) }}
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('ruc', 'RUC Cliente') }}
                                {{ Form::text('ruc', null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                {{ Form::label('client', 'Cliente') }}
                                {{ Form::text('client', null, array('class' => 'form-control')) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('daterange', 'Rango Fechas de Emisión') }}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    {{ Form::text('daterange', null, array('class' => 'form-control pull-right')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="box-footer">
                    {{ Form::submit('Buscar', array('class' => 'btn btn-primary'))}}
                    {{ Form::reset('Limpiar', array('class' => 'btn btn-default btn_reset'))}}
                </div>
                {{ Form::close() }}


                @if(!empty($reports) && $reports->count())
                <div class="callout callout-info">
                    <p>Se encontraron {{$reports->count()}} registros.</p>
                </div>
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="">#</th>
                                    <th class="">Tipo Documento</th>
                                    <th class="">Número</th>
                                    <th class="">Fecha emisión</th>
                                    <th class="">Código Externo</th>
                                    <th class="">Estado</th>
                                    <th class="">Fecha creación </th>
                                    <th class="">Descargas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $key => $value)
                                <tr>
                                    <td>{{$value->id}}</td>
                                    <td>{{$value->document_type_code}}</td>
                                    <td>{{$value->series}}-{{$value->number}}</td>
                                    <td>{{$value->date_of_issue->format('Y-m-d')}}</td>
                                    <td>{{$value->external_id}}</td>
                                    <td>{{$value->state_type->description}}</td>
                                    <td>{{$value->created_at}}</td>
                                    <td class="">
                                        @if ($value->external_id)
                                        <div>
                                            <i class="fa fa-download"></i>
                                            <a href="{{asset($value->download_pdf)}}" class="descarga" target="_blank">PDF</a>
                                        </div>
                                        <div>
                                            <i class="fa fa-download"></i>
                                            <a href="{{asset($value->download_xml)}}" class="descarga" target="_blank">XML</a>
                                        </div>
                                            @if($value->state_type_id !== '01')
                                        <div>
                                            <i class="fa fa-download"></i>
                                            <a href="{{asset($value->download_cdr)}}" class="descarga" target="_blank">CDR</a>
                                        </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper">

                            {!! $reports->appends(['search' => Session::get('form_document_list')])->render() !!}

                        </div>
                    </div>
                </div>
                @else
                <div class="callout callout-info">
                    <p>No se encontraron registros.</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')

<script>

    $('.btn_reset').on('click', function (e) {
        e.preventDefault()
        reset()
    })

    function reset(){

        $('form input.form-control').val('')
        $('form select.form-control').val(0)

    }

    $('#daterange').daterangepicker({
        format: 'YYYY-MM-DD',
        autoApply: true,
        locale: {
            applyLabel: 'Aceptar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        }
    })

    @if(!isset(Session::get('form_document_list')['daterange']))
      $("#daterange").val('')
    @endif


</script>

@endpush


