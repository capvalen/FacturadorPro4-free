<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>A. Fijos - Compras</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte A. Fijos - Compras</strong></h3>
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
                    <td>
                        <p><strong>Establecimiento: </strong></p>
                    </td>
                    <td align="center">{{$establishment->address}} - {{$establishment->department->description}} - {{$establishment->district->description}}</td>
                </tr>
                
                @inject('reportService', 'Modules\Report\Services\ReportService')
                <tr>
                    @if($filters['seller_id'])
                    <td>
                        <p><strong>Usuario: </strong></p>
                    </td>
                    <td align="center">
                        {{$reportService->getUserName($filters['seller_id'])}}
                    </td>
                    @endif
                    @if($filters['person_id'])
                    <td>
                        <p><strong>Proveedor: </strong></p>
                    </td>
                    <td align="center">
                        {{$reportService->getPersonName($filters['person_id'])}}
                    </td>
                    @endif
                </tr>

            </table>
        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    @php
                        $acum_total_taxed=0;
                        $acum_total_igv=0;
                        $acum_total=0;
                     
                        $acum_total_taxed_usd=0;
                        $acum_total_igv_usd=0;
                        $acum_total_usd=0;
                    @endphp
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo Doc</th>
                                <th>Número</th>
                                <th>F. Emisión</th>
                                <th class="">F. Vencimiento</th>

                                <th>Cliente</th>
                                <th>RUC</th>
                                {{-- <th class="">F. Pago</th> --}}
                                <th>Estado</th>
                                <th>Moneda</th>
                                <th>Percepción</th>
                                <th class="" >T.Exonerado</th>

                                <th class="" >T.Inafecta</th>
                                <th class="" >T.Gratuito</th>
                                <th>Total Gravado</th>
                                <th>Total IGV</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->document_type->id}}</td>
                                <td class="celda">{{$value->series}}-{{$value->number}}</td>
                                <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td>
                                <td class="celda">{{$value->date_of_due->format('Y-m-d')}}</td>

                                <td class="celda">{{$value->supplier->name}}</td>
                                <td class="celda">{{$value->supplier->number}}</td>
                                {{-- <td class="celda">{{isset($value->purchase_payments['payment_method_type']['description'])?$value->purchase_payments['payment_method_type']['description']:'-'}}</td> --}}
                                <td class="celda">{{$value->state_type->description}}</td>
                                <td class="celda">{{$value->currency_type_id}}</td> 
                                <td class="celda">{{$value->state_type_id == '11' ? 0 : $value->total_perception}}</td>

                                <td class="celda">{{$value->state_type_id == '11' ? 0 : $value->total_exonerated}}</td>
                                <td class="celda">{{ $value->state_type_id == '11' ? 0 : $value->total_unaffected}}</td>
                                <td class="celda">{{ $value->state_type_id == '11' ? 0 : $value->total_free}}</td>

                                <td class="celda">{{$value->state_type_id == '11' ? 0 : $value->total_taxed}}</td>
                                <td class="celda">{{$value->state_type_id == '11' ? 0 : $value->total_igv}}</td>
                                <td class="celda">{{$value->state_type_id == '11' ? 0 : $value->total + $value->total_perception}}</td>

                                
                                @php
                                    $value->total_taxed = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed;
                                    $value->total_igv = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv;
                                    $value->total = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total;
                                    $state = $value->state_type_id;
                                @endphp
                            </tr>
                            
                            @php
                                
                                if($value->currency_type_id == 'PEN'){

                                    if($state == '11'){

                                        $acum_total += 0;
                                        $acum_total_taxed += 0;
                                        $acum_total_igv += 0;


                                    }else{

                                        $acum_total += $value->total;
                                        $acum_total_taxed += $value->total_taxed;
                                        $acum_total_igv += $value->total_igv; 
                                    }

                                }else if($value->currency_type_id == 'USD'){

                                    if($state == '11'){

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
                                <td class="celda" colspan="12"></td>
                                <td class="celda" >Totales PEN</td>
                                <td class="celda">{{$acum_total_taxed}}</td>
                                <td class="celda">{{$acum_total_igv}}</td>
                                <td class="celda">{{$acum_total}}</td>
                            </tr>
                            <tr>
                                <td class="celda" colspan="12"></td>
                                <td class="celda" >Totales USD</td>
                                <td class="celda">{{$acum_total_taxed_usd}}</td>
                                <td class="celda">{{$acum_total_igv_usd}}</td>
                                <td class="celda">{{$acum_total_usd}}</td>
                            </tr>
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
