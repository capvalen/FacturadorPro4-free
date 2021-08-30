<table>
    <tr>
        <td></td>
        <td>ADVENIRFAC</td>
    </tr>
    <tr>
        <td></td>
        <td>REPORTE DE VENTAS</td>
    </tr>
    <tr>
        <td></td>
        <td>PARA SUMERIUS</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>

    </tr>
    <tr>
        <td></td>
        <td></td>

    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>CTA1</td>
        <td>CTA1</td>
        <td>CTA3</td>
        <td>CTA2</td>
        <td>CTA4</td>
        <td>CTA1</td>
        <td>CTA5</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>H</td>
        <td>H</td>
        <td>H</td>
        <td>H</td>
        <td>D</td>

    </tr>

    <tr>
        <td>NUMERO</td>
        <td>FECHAE</td>
        <td>FECHAV</td>
        <td>TIPOC</td>
        <td>SERIE</td>
        <td>NUMERO</td>
        <td>NUMER2</td>
        <td>TIPODOC</td>
        <td>DOCUMENTO</td>
        <td>NOMBRE</td>
        <td>EXPORTACION</td>
        <td>BASEI</td>
        <td>ICBPER</td>
        <td>EXONERADO</td>
        <td>IGV</td>
        <td>RETENCION</td>
        <td>TOTAL</td>
        <td>TIPO DE CAMBIO</td>
        <td>FECHA NOTA DE CREDITO</td>
        <td>TIPO DE DOC</td>
        <td>SERIE - NUMERO</td>
        <td>CTA1</td>
        <td>CTA2</td>
        <td>CTA3</td>
        <td>CTA4</td>
        <td>CTA5</td>
        <td>CLOSA</td>
    </tr>
    @foreach($records as $row)
    @if($row['state_type_id'] == '11')
    <tr>
        <td>{{ $row['col_A'] }}</td>
        <td>{{ $row['date_of_issue'] }}</td>
        <td>{{ $row['date_of_due'] }}</td>
        <td>{{ $row['document_type_id'] }}</td>
        <td>{{ $row['series'] }}</td>
        <td>{{ $row['number'] }}</td>
        <td>{{ $row['col_G'] }}</td>
        <td>0</td>
        <td>0</td>
        <td>ANULADA</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>{{ $row['col_S'] }}</td>
        <td>{{ $row['col_T'] }}</td>
        <td>{{ $row['col_U'] }}</td>
        <td>{{ $row['col_V'] }}</td>
        <td>{{ $row['col_W'] }}</td>
        <td>{{ $row['col_X'] }}</td>
        <td>{{ $row['col_Y'] }}</td>
        <td>{{ $row['col_Z'] }}</td>
        <td>ANULADA</td>

    </tr>
    @else
    <tr>
        <td>{{ $row['col_A'] }}</td>
        <td>{{ $row['date_of_issue'] }}</td>
        <td>{{ $row['date_of_due'] }}</td>
        <td>{{ $row['document_type_id'] }}</td>
        <td>{{ $row['series'] }}</td>
        <td>{{ $row['number'] }}</td>
        <td>{{ $row['col_G'] }}</td>
        <td>{{ $row['customer_identity_document_type_id'] }}</td>
        <td>{{ $row['customer_number'] }}</td>
        <td>{{ $row['customer_name'] }}</td>
        <td>{{ $row['total_exportation'] }}</td>
        <td>{{ $row['total_taxed'] }}</td>
        <td>{{ $row['total_exonerated'] }}</td>
        <td>{{ $row['total_unaffected'] }}</td>
        <td>{{ $row['total_igv'] }}</td>
        <td>{{ $row['total_isc'] }}</td>
        <td>{{ $row['total'] }}</td>
        <td>{{ $row['total_plastic_bag_taxes'] }}</td>
        <td>{{ $row['col_S'] }}</td>
        <td>{{ $row['col_T'] }}</td>
        <td>{{ $row['col_U'] }}</td>
        <td>{{ $row['col_V'] }}</td>
        <td>{{ $row['col_W'] }}</td>
        <td>{{ $row['col_X'] }}</td>
        <td>{{ $row['col_Y'] }}</td>
        <td>{{ $row['col_Z'] }}</td>
        <td>{{ $row['col_AA'] }}</td>

    </tr>
    @endif
    @endforeach
</table>
