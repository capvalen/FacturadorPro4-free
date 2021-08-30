@php
    $accepted_documents = $records['accepted_documents'];
    $voided_documents = $records['voided_documents'];
    $totals_accepted_documents = $records['totals_accepted_documents'];
    $totals_voided_documents = $records['totals_voided_documents'];
@endphp
<br>
<h4>CONFIRMADOS</h4>
<br>
<table>    
    <tr>
        <th>Comprobante</th>
        <th>Serie</th>
        <th class="text-center">N° Inicial</th>
        <th class="text-center">N° Final</th>
        <th class="text-right">Valor venta</th>
        <th class="text-right">IGV</th>
        <th class="text-right">ICBPER</th>
        <th class="text-right">Importe</th>
    </tr> 
    @foreach($accepted_documents as $row)
    <tr>
        <td>{{$row['document_type_description']}}</td>
        <td>{{$row['series']}}</td>
        <td class="text-center">{{$row['start_number']}}</td>
        <td class="text-center">{{$row['end_number']}}</td>
        <td class="text-right">{{$row['total_value']}}</td>
        <td class="text-right">{{$row['total_igv']}}</td>
        <td class="text-right">{{$row['total_plastic_bag_taxes']}}</td>
        <td class="text-right">{{$row['total']}}</td>
    </tr>
    @endforeach
    <tr>
        <td class="text-right" colspan="4">Total</td>
        <td class="text-right">{{$totals_accepted_documents['general_total_value']}}</td>
        <td class="text-right">{{$totals_accepted_documents['general_total_igv']}}</td>
        <td class="text-right">{{$totals_accepted_documents['general_total_plastic_bag_taxes']}}</td>
        <td class="text-right">{{$totals_accepted_documents['general_total']}}</td>
    </tr>
</table>

<br>
<br>
<h4>INVALIDADOS</h4>
<br>
<table>    
    <tr>
        <th>Comprobante</th>
        <th>Serie</th>
        <th class="text-center">Numeros</th> 
        <th class="text-right">Importe</th>
    </tr> 
    @foreach($voided_documents as $row)
    <tr>
        <td>{{$row['document_type_description']}}</td>
        <td>{{$row['series']}}</td>
        <td class="text-center">{{$row['voided']}}</td> 
        <td class="text-right">{{$row['total']}}</td>
    </tr>
    @endforeach
    <tr>
        <td class="text-right" colspan="3">Total</td> 
        <td class="text-right">{{$totals_voided_documents['general_total']}}</td>
    </tr>
</table>