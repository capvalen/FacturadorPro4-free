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
        <td colspan="{{ $col_span }}" class="text-center font-weight">FORMATO 8.1 : "REGISTRO DE COMPRAS DEL PERIODO {{ $period }}"</td>
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
            COMPROBANTE DE PAGO O DOCUMENTO
        </td>
        <td>
            Nro. DEL COMPROBANTE DE PAGO O DOCUMENTO
        </td>
        <td colspan="3">
            INFORMACON DE PROVEEDOR
        </td>
        <td colspan="2">
            ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION
        </td>
        <td colspan="2">
            ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION Y A OPERACIONES NO GRAVADAS
        </td>
        <td colspan="2">
            ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS
        </td>
        <td>
            VALOR DE LAS ADQUISICIONES NO GRAVADAS
        </td>
        <td>
            ISC
        </td>
        <td>
            OTROS TRIBUTOS Y CARGOS
        </td>
        <td>
            NUMERO DE COMPROBANTE DE PAGO EMITIDO POR SUJETO NO DOMICILIADO
        </td>
        <td>
            IMPORTE TOTAL
        </td>
        <td>
            MONEDA
        </td>
        <td colspan="2">
            CONSTANCIA DE DEPOSITO DE DETRACCION
        </td>
        <td>
            TIPO DE CAMBIO
        </td>
        <td colspan="4">
            REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA
        </td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td></td>
        <td></td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>AÑO DE EMISION DE DUA</td>
        <td></td>
        <td>TIPO</td>
        <td>NÚMERO</td>
        <td>APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL</td>
        <td>BASE IMPONIBLE</td>
        <td>IGV</td>
        <td>BASE IMPONIBLE</td>
        <td>IGV</td>
        <td>BASE IMPONIBLE</td>
        <td>IGV</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>NÚMERO</td>
        <td>FECHA DE EMISIÓN</td>
        <td></td>
        <td>FECHA</td>
        <td>TIPO</td>
        <td>SERIE</td>
        <td>NÚMERO</td>
    </tr>
    @foreach($records as $row)
    <tr>
        <td>05</td>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $row['date_of_issue'] }}</td>
        <td>
            @if($row['document_type_id'] == '14')
                {{ $row['date_of_due'] }}
            @endif
        </td>
        <td>{{ $row['document_type_id'] }}</td>
        <td>{{ $row['series'] }}</td>
        <td></td>
        <td>{{ $row['number'] }}</td>
        <td>{{ $row['supplier_identity_document_type_id'] }}</td>
        <td>{{ $row['supplier_number'] }}</td>
        <td>{{ $row['supplier_name'] }}</td>
        <td></td>
        <td></td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_taxed'] }}</td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total_igv'] }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ (in_array($row['document_type_id'],['01','03']) && in_array($row['state_type_id'],['09','11'])) ? 0 :  $row['total'] }}</td>
        <td>{{ $row['currency_type_symbol'] }}</td>
        <td></td>
        <td></td>
        <td>{{ $row['exchange_rate_sale'] }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endforeach
</table>