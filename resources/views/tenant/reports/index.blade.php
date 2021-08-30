@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div>
                        <h4 class="card-title">Consulta de Documentos</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <form action="{{route('tenant.search')}}" class="el-form demo-form-inline el-form--inline" method="GET">
                            <tenant-calendar :document_types="{{json_encode($documentTypes)}}" :establishments="{{json_encode($establishments)}}" establishment="{{$establishment ?? null}}" data_d="{{$d ?? ''}}" data_a="{{$a ?? ''}}" td="{{$td ?? null}}"></tenant-calendar>
                        </form>
                    </div>
                    @if(!empty($reports) && $reports->count())
                    <div class="box">
                        <div class="box-body no-padding">
                            <div style="margin-bottom: 10px">
                                @if(isset($reports))
                                    <form action="{{route('tenant.report_pdf')}}" class="d-inline" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" value="{{$d}}" name="d">
                                        <input type="hidden" value="{{$a}}" name="a">
                                        <input type="hidden" value="{{$td}}" name="td">
                                        <input type="hidden" value="{{$establishment}}" name="establishment">
                                        <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-pdf"></i> Exportar PDF</button>
                                        {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                    </form>
                                <form action="{{route('tenant.report_excel')}}" class="d-inline" method="POST">
                                    {{csrf_field()}}
                                    <input type="hidden" value="{{$d}}" name="d">
                                    <input type="hidden" value="{{$td}}" name="td">
                                    <input type="hidden" value="{{$a}} " name="a">
                                    <input type="hidden" value="{{$establishment}}" name="establishment">
                                    <button class="btn btn-custom   mt-2 mr-2" type="submit"><i class="fa fa-file-excel"></i> Exportar Excel</button>
                                    {{-- <label class="pull-right">Se encontraron {{$reports->count()}} registros.</label> --}}
                                </form>
                                @endif
                            </div>
                            @php
                                $acum_total_taxed=0;
                                $acum_total_igv=0;
                                $acum_total=0;
                              
                                $serie_affec = '';

                                $acum_total_exonerado=0;
                                $acum_total_inafecto=0;
                             
                                $acum_total_free=0;
 
                                $acum_total_taxed_usd=0;
                                $acum_total_igv_usd=0;
                                $acum_total_usd=0;
                            @endphp
                            <table width="100%" class="table table-striped table-responsive table-bordered table-hover">
                                <thead class="">
                                    <tr>
                                        <th class="">#</th>
                                        <th class="">Tipo Documento</th>
                                        <th class="">Comprobante</th>
                                        <th class="">Fecha emisión</th>
                                        <th>Doc. Afectado</th>
                                        <th class="">Cliente</th>
                                        <th class="">RUC</th>
                                        <th class="">Estado</th>
                                        <th class="">Moneda</th>
                                        <th class="">Total Exonerado</th>
                                        <th class="">Total Inafecto</th>
                                        <th class="">Total Gratuito</th>
                                        <th class="">Total Gravado</th>
                                      
                                        <th class="">Total IGV</th>
                                        <th class="">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $key => $value)
                                     @if(in_array($value->document_type_id,["07","08"]) && $value->note)

                                          @php 
                                            $serie = ($value->note->affected_document) ? $value->note->affected_document->series : $value->note->data_affected_document->series;
                                            $number =  ($value->note->affected_document) ? $value->note->affected_document->number : $value->note->data_affected_document->number;
                                            $serie_affec = $serie.' - '.$number;

                                          @endphp
                                        

                                    @endif
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->document_type->id}}</td>
                                        <td>{{$value->series}}-{{$value->number}}</td>
                                        <td>{{$value->date_of_issue->format('Y-m-d')}}</td>
                                         
                                        
                                        
                                       
                                        <td>{{$serie_affec}} </td>
                                        <td>{{$value->person->name}}</td>
                                        <td>{{$value->person->number}}</td>
                                        <td>{{$value->state_type->description}}</td>

                                        @php
                                         $signal = $value->document_type_id;
                                         $state = $value->state_type_id;
                                        @endphp
                                      
                                        <td class="celda">{{$value->currency_type_id}}</td>
                                        
                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_exonerated}} </td>
                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_unaffected}}</td>
                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_free}}</td>

                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_taxed}}</td>
                                      
                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total_igv}}</td>
                                        <td>{{($signal == '07' || ($signal!='07' && $state =='11')) ? "-" : ""  }}{{$value->total}}</td>
                                   
                                    @php
                                        $serie_affec =  '';
                                    @endphp
  
                                    </tr>
                                    @php
                                        if($value->currency_type_id == 'PEN'){
                                            /*$acum_total_taxed +=  $signal != '07' ? $value->total_taxed : -$value->total_taxed ;
                                            $acum_total_igv +=  $signal != '07' ? $value->total_igv : -$value->total_igv ;
                                            $acum_total += $signal != '07' ? $value->total : -$value->total ;*/

                                            /*$acum_total_exonerado += $signal != '07' ? $value->total_exonerated : -$value->total_exonerated ;                                            
                                            $acum_total_inafecto += $signal != '07' ? $value->total_unaffected : -$value->total_unaffected ;
                                            $acum_total_free += $signal != '07' ? $value->total_free : -$value->total_free ;*/

 
                                            if(($signal == '07' && $state !== '11')){

                                                $acum_total += -$value->total;
                                                $acum_total_taxed += -$value->total_taxed;
                                                $acum_total_igv += -$value->total_igv;

                                                
                                                $acum_total_exonerado += -$value->total_exonerated;
                                                $acum_total_inafecto += -$value->total_unaffected;
                                                $acum_total_free += -$value->total_free;


                                            }elseif($signal != '07' && $state == '11'){

                                                $acum_total += 0;
                                                $acum_total_taxed += 0;
                                                $acum_total_igv += 0;

                                                $acum_total_exonerado += 0;
                                                $acum_total_inafecto += 0;
                                                $acum_total_free += 0;

                                            }else{

                                                $acum_total += $value->total;
                                                $acum_total_taxed += $value->total_taxed;
                                                $acum_total_igv += $value->total_igv;

                                                $acum_total_exonerado += $value->total_exonerated;
                                                $acum_total_inafecto += $value->total_unaffected;
                                                $acum_total_free += $value->total_free;
                                            }
  

                                        }else if($value->currency_type_id == 'USD'){ 
                                            
                                            if(($signal == '07' && $state !== '11')){

                                                $acum_total_usd += -$value->total;
                                                $acum_total_taxed_usd += -$value->total_taxed;
                                                $acum_total_igv_usd += -$value->total_igv;
 


                                            }elseif($signal != '07' && $state == '11'){

                                                $acum_total_usd += 0;
                                                $acum_total_taxed_usd += 0;
                                                $acum_total_igv_usd += 0;
 

                                            }else{

                                                $acum_total_usd += $value->total;
                                                $acum_total_taxed_usd += $value->total_taxed;
                                                $acum_total_igv_usd += $value->total_igv;
 
                                            }

                                            
                                        }

                                    @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="8"></td>
                                      
                                        <!-- <td>Totales</td>
                                        <td>{{$acum_total_exonerado}}</td>
                                        <td>{{$acum_total_inafecto}}</td>
                                        <td>{{$acum_total_free}}</td> -->
                                        <td >Totales PEN</td>
                                        <td>{{number_format($acum_total_exonerado, 2)}}</td>
                                        <td>{{number_format ($acum_total_inafecto, 2 )}}</td>
                                        <td>{{number_format($acum_total_free, 2)}}</td>

                                        <td>{{$acum_total_taxed}}</td>
                                        <td>{{$acum_total_igv}}</td>
                                        <td>{{$acum_total}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="8"></td>
                                        <td >Totales USD</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        
                                        <td>{{$acum_total_taxed_usd}}</td>
                                        <td>{{$acum_total_igv_usd}}</td>
                                        <td>{{$acum_total_usd}}</td>
                                    </tr>
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
