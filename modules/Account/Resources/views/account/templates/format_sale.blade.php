<style>
    .text-center {
        text-align: center;
    }
    .font-weight {
        font-weight: bold;
    }
</style>
@php
$col_span = 25;
@endphp
<table>
    <tr>
        <td colspan="{{ $col_span }}">{{ $company['name'] }}</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}">{{ $company['number'] }}</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}">Moneda: SOLES</td>
    </tr>
    <tr>
        <td colspan="{{ $col_span }}" class="text-center font-weight">FORMATO 14.1 : "REGISTRO DE VENTAS E INGRESOS  DEL PERIODO {{ $period }}"</td>
    </tr>
    <tr>
        <td colspan="2">
            NUMERO CORRELATIVO DEL REGISTRO O CUO.
        </td>
        <td>
            FECHA DE EMISION DEL COMPROBANTE DE PAGO O EMISION DEL DOCUMENTO
        </td>
        <td>
            FECHA VENC.
        </td>
        <td colspan="3">
            COMPROBANTE DE PAGO
        </td>
        <td colspan="3">
            INFORMACON DE CLIENTE
        </td>
        <td>
            VALOR<br/>FACTURADO<br/>EXPORTACION
        </td>
        <td>
            BASE<br/>IMPONIBLE<br/>GRAVADA
        </td>
        <td colspan="2">
            IMPORTE TOTAL
        </td>
        <td>
            ISC
        </td>
        <td>VENTA DIFERIDA</td>
        <td>
            IGV Y/O<br/>IMP.
        </td>
        <td>
            OTROS<br/>TRIBUTOS
        </td>
        <td>
            IMPORTE TOTAL
        </td>
        <td>
            TIPO DE<br/>CAMBIO
        </td>
        <td>
            MONEDA
        </td>
        <td colspan="4">
            REFERENCIA DEL COMPROBANTE O<br/>
            DOC. ORIGINAL QUE SE MODIFICA
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td></td>
        <td></td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>NUMERO</td>
        <td>TIPO</td>
        <td>R.U.C.</td>
        <td>APELLIDOS Y NOMBRES</td>
        <td></td>
        <td></td>
        <td>EXONERADA</td>
        <td>INAFECTA</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>FECHA</td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>Nro.COMP.</td>
    </tr>
    @foreach($records as $row)
    <tr>
        <td>06</td>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $row['date_of_issue'] }}</td>
        <td></td>
        <td>{{ $row['document_type_id'] }}</td>
        <td>{{ $row['series'] }}</td>
        <td>{{ $row['number'] }}</td>
        <td>{{ $row['customer_identity_document_type_id'] }}</td>
        <td>{{ $row['customer_number'] }}</td>
        <td>{{ $row['customer_name'] }}</td>

        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 : $row['total_exportation'] }}</td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_taxed'] }}</td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_exonerated'] }}</td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_unaffected'] }}</td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_plastic_bag_taxes'] }}</td>
        <td></td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_igv'] }}</td>
        <td></td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total'] }}</td>

        <td>{{ $row['exchange_rate_sale'] }}</td>
        <td>{{ $row['currency_type_symbol'] }}</td>
        @if($row['affected_document'])
            <td>{{ $row['affected_document']['date_of_issue']}}</td>
            <td>{{ $row['affected_document']['document_type_id']}}</td>
            <td>{{ $row['affected_document']['series']}}</td>
            <td>{{ $row['affected_document']['number']}}</td>
        @else
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        @endif
    </tr>
    @endforeach
</table>
