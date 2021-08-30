<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>RH</title>
    </head>
    <body>
        <div>
            <h3 align="center" class="title"><strong>Reporte Análisis comercial</strong></h3>
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
                  
                </tr>
            </table>
        </div>
        <br>
        @if(!empty($records))
            <div class="">
                <div class=" ">
                    <table class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="">Cliente</th>
                                <th class="">T. Doc - N° Doc</th>
                                <th class="">Zona</th>
                                <th class="">Celular</th> 
                                <th class="">Primera compra</th> 
                                <th class="">Ultima compra</th> 
                                <th class="">Tiempo promedio entre dos compras en dia</th> 
                                <th class="">Cantidad de Visita</th> 
                                <th class="">Total</th> 

                                @foreach($categories as $item)
                                <th class="">{{$item}}</th> 
                                @endforeach
                                <!--<th class="">Cinta</th> 
                                <th class="">Disco</th> 
                                <th class="">Cuchilla</th> 
                                <th class="">Estelitado</th> 
                                <th class="">Servicio</th> 
                                <th class="">Accesorios</th> -->
                                <th class="">Contactar el</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $key => $value)
                            @php
                            
                                $customer = $value;
                                $documents = $value->documents;

                                $country =($customer->country_id)? $customer->country->description : '' ;
                                $district = ($customer->district_id)? '-'.$customer->district->description : '' ;
                                $province = ($customer->province_id)? '-'.$customer->province->description : '' ;
                                $department = ($customer->department_id)? '-'.$customer->department->description : '' ;
                                $zone = "{$country} {$department} {$province} {$district}";

                                $first_document_date = ($documents) ? ($documents->first() ? $documents->first()->date_of_issue->format('d-m-Y'):null):null;
                                $last_document_date = ($documents) ? ($documents->last() ? $documents->last()->date_of_issue->format('d-m-Y'):null):null;
                                $difference_days = null;
                                $quantity_visit = $documents->count();
                                $total = $documents->sum('total');
 
                                $acum_difference_days = 0;
                                $acum_comparations = 0;

                                
                                if($first_document_date && $last_document_date){
                                    for ($i=0; $i < $quantity_visit; $i++) { 

                                        $doc = $documents[$i];
                                        // dd($doc->date_of_issue);
                                        if(($i+1) < $quantity_visit){
                                            
                                            $f_date = $doc->date_of_issue;
                                            $acum_difference_days += $f_date->diffInDays($documents[$i+1]->date_of_issue);
                                            $acum_comparations++;
                                        }
                                    }
                                }

                                $prom_difference_days = ($acum_comparations > 0) ? number_format($acum_difference_days / $acum_comparations,2) : 0;

                                $contact_date = (Carbon\Carbon::parse($last_document_date))->addDays($prom_difference_days);

                                $calculate_categories_count = array();
                                foreach ($categories as $item) {
                                    $calculate_categories_count[strtoupper($item)] = 0;
                                }

                            

                                if($quantity_visit > 0){
                                    foreach ($documents as $doc) {
                                        foreach ($doc->items as $it) {
                                            $item = App\Models\Tenant\Item::findOrFail($it->item_id);

                                            if($item->category){

                                                $name_category = strtoupper($item->category->name);
                                                $calculate_categories_count[$name_category] = $calculate_categories_count[$name_category] + $it->quantity;

                                            }
                                            
                                        }
                                    }
                                }



                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td> 
                                <td>{{$customer->name}}</td>
                                <td>{{ $customer->identity_document_type->description}} - {{ $customer->number }}<br/></td> 
                                <td>{{$zone}}</td> 
                                <td>{{$customer->telephone}}</td> 
                                <td>{{$first_document_date}}</td> 
                                <td>{{$last_document_date}}</td> 

                                <td>{{$prom_difference_days}}</td>
                                <td>{{$quantity_visit}}</td>
                                <td>{{$total}}</td>

                                @foreach($calculate_categories_count as $item)
                                 <th class="">{{$item}}</th> 
                                @endforeach
                               
                                <td>{{$contact_date->format('d-m-Y')}}</td>
                        
                            </tr>
                            @endforeach
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
