<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>REPORTE POR CLIENTE</strong></h3>
        </div>
        <br>
        <div style="margin-top:20px; margin-bottom:15px;">
            <table>
                @if(!empty($records))
                    <tr>
                        <td>
                            <p><b>Cliente: </b></p>
                        </td>
                        <td align="center">
                            <p><strong>{{$records[0]->customer->number}} - {{$records[0]->customer->name}}</strong></p>
                        </td> 
                    </tr>
                @endif
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
                      
                        $serie_affec = '';
                        $acum_total_exonerado=0;
                        $acum_total_inafecto=0;

                        $acum_total_free=0;

                        $acum_total_taxed_usd = 0;
                        $acum_total_igv_usd = 0;
                        $acum_total_usd = 0;
                    @endphp
                    <table class="">
                        <thead>
                            <tr>
                                <th class="">#</th>
                                <th class="">Fecha</th>
                                <th class="">Tipo Documento</th>
                                <th class="">Serie</th>
                                <th class="">Número</th>
                                <th class="">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            <tr>
                                <td class="celda">{{$loop->iteration}}</td>
                                <td class="celda">{{$value->date_of_issue->format('Y-m-d')}}</td> 
                                <td class="celda">{{$value->document_type->description}}</td>
                                <td class="celda">{{$value->series}}</td>
                                <td class="celda">{{$value->number}}</td>
                                <td class="celda">{{$value->total}}</td>
                               
                                @php
                                  $signal = $value->document_type_id;
                                  $state = $value->state_type_id;
                                @endphp 
                                
                                
                                
                                @php
                                
                                    $value->total_exonerated = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_exonerated;
                                    $value->total_unaffected = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_unaffected;
                                    $value->total_free = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_free;

                                    $value->total_taxed = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_taxed;
                                    $value->total_igv = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total_igv;
                                    $value->total = (in_array($value->document_type_id,['01','03']) && in_array($value->state_type_id,['09','11'])) ? 0 : $value->total;
                                @endphp

                            @php
                              
                                $serie_affec =  '';
                              
                            @endphp
 
                            </tr>
                            @php
                            if($value->currency_type_id == 'PEN'){ 


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
                                <td colspan="4"></td> 
                                <td >TOTAL PEN</td>
                                <td>{{$acum_total}}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td >TOTAL USD</td>  
                                <td>{{$acum_total_usd}}</td>
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
