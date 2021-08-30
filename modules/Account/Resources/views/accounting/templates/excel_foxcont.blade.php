<table>
    <tr>
        <td>FECHAE</td>
        <td>FECHAV</td>
        <td>TIPOC</td>
        <td>SERIE</td>
        <td>NUMERO</td>
        <td>TIPODOC</td>
        <td>DOCUMENTO</td>
        <td>NOMBRE</td>
        <td>BASEI</td>
        <td>IGV</td>
        <td>ICBPER</td>
        <td>EXONERADO</td>
        <td>RETENCION</td>
        <td>TOTAL</td>
    </tr>
    @foreach($records as $row)
        @if($row['state_type_id'] == '11')
            <tr>
                <td>{{ $row['date_of_issue'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['document_type_id'] }}</td>
                <td>{{ $row['series'] }}</td>
                <td>{{ $row['number'] }}</td>
                <td>0</td>
                <td>0</td> 
                <td>ANULADA</td>
                <td>0</td> 
                <td>0</td> 
                <td>0</td> 
                <td>0</td> 
                <td>0</td> 
                <td>0</td> 
            </tr>
        @else
            <tr>
                <td>{{ $row['date_of_issue'] }}</td>
                <td>{{ $row['date_of_due'] }}</td>
                <td>{{ $row['document_type_id'] }}</td>
                <td>{{ $row['series'] }}</td>
                <td>{{ $row['number'] }}</td>
                <td>{{ $row['customer_identity_document_type_id'] }}</td>
                <td>{{ $row['customer_number'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                {{-- <td>{{ $row['total_exportation'] }}</td> --}}
                <td>{{ $row['total_taxed'] }}</td>
                <td>{{ $row['total_igv'] }}</td>
                {{-- <td>{{ $row['total_unaffected'] }}</td> --}}
                {{-- <td>{{ $row['total_isc'] }}</td> --}}
                <td>{{ $row['total_plastic_bag_taxes'] }}</td>
                <td>{{ $row['total_exonerated'] }}</td>
                <td>{{ $row['total_retention'] }}</td>
                <td>{{ $row['total'] }}</td>
            </tr>
        @endif
    @endforeach
</table>
