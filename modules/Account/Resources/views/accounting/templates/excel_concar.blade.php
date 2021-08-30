
<table>
    <thead>
        <tr>
            <td>Sub Diario</td>
            <td>Número de Comprobante</td>
            <td>Fecha de Comprobante</td>
            <td>Código de Moneda</td>
            <td>Glosa Principal</td>
            <td>Tipo de Cambio</td>
            <td>Tipo de Conversión</td>
            <td>Flag de Conversión de Moneda</td>
            <td>Fecha Tipo de Cambio</td>
            <td>Cuenta Contable</td>
            <td>Código de Anexo</td>
            <td>Código de Centro de Costo</td>
            <td>Debe / Haber</td>
            <td>Importe Original</td>
            <td>Importe en Dólares</td>
            <td> Importe en Soles</td>
            <td>Tipo de Documento</td>
            <td>Número de Documento</td>
            <td>Fecha de Documento</td>
            <td>Fecha de Vencimiento</td>
            <td>Código de Area</td>
            <td>Glosa Detalle</td>
            <td>Código de Anexo Auxiliar</td>
            <td>Medio de Pago</td>
            <td>Tipo de Documento de Referencia</td>
            <td>Número de Documento Referencia</td>
            <td>Fecha Documento Referencia</td>
            <td>Nro Máq. Registradora Tipo Doc. Ref.</td>
            <td>Base Imponible Documento Referencia</td>
            <td>IGV Documento Provisión</td>
            <td>Tipo Referencia en estado MQ</td>
            <td>Número Serie Caja Registradora</td>
            <td>Fecha de Operación</td>
            <td>Tipo de Tasa</td>
            <td>Tasa Detracción/Percepción</td>
            <td>Importe Base Detracción/Percepción Dólares</td>
            <td>Importe Base Detracción/Percepción Soles</td>
            <td>Tipo Cambio para 'F'</td>
            <td>Importe de IGV sin derecho crédito fiscal</td> 
        </tr>
        {{-- <tr>
            <td>Restricciones</td>
            <td>Ver T.G. 02</td>
            <td>Los dos primeros dígitos son el mes y los otros 4 siguientes un correlativo</td>
            <td></td>
            <td>Ver T.G. 03</td>
            <td></td>
            <td>Llenar  solo si Tipo de Conversión es 'C'. Debe estar entre >=0 y <=9999.999999</td>
            <td>Solo: 'C'= Especial, 'M'=Compra, 'V'=Venta , 'F' De acuerdo a fecha</td>
            <td>Solo: 'S' = Si se convierte, 'N'= No se convierte</td>
            <td>Si  Tipo de Conversión 'F'</td>
            <td>Debe existir en el Plan de Cuentas</td>
            <td>Si Cuenta Contable tiene seleccionado Tipo de Anexo, debe existir en la tabla de Anexos</td>
            <td>Si Cuenta Contable tiene habilitado C. Costo, Ver T.G. 05</td>
            <td>'D' ó 'H'</td>
            <td>Importe original de la cuenta contable. Obligatorio, debe estar entre >=0 y <=99999999999.99</td>
            <td>Importe de la Cuenta Contable en Dólares. Obligatorio si Flag de Conversión de Moneda esta en 'N', debe estar entre >=0 y <=99999999999.99</td>
            <td>Importe de la Cuenta Contable en Soles. Obligatorio si Flag de Conversión de Moneda esta en 'N', debe estra entre >=0 y <=99999999999.99</td>
            <td>Si Cuenta Contable tiene habilitado el Documento Referencia Ver T.G. 06</td>
            <td>Si Cuenta Contable tiene habilitado el Documento Referencia Incluye Serie y Número</td>
            <td>Si Cuenta Contable tiene habilitado el Documento Referencia</td>
            <td>Si Cuenta Contable tiene habilitada la Fecha de Vencimiento</td>
            <td>Si Cuenta Contable tiene habilitada el Area. Ver T.G. 26</td>
            <td></td>
            <td>Si Cuenta Contable tiene seleccionado Tipo de Anexo Referencia</td>
            <td>Si Cuenta Contable tiene habilitado Tipo Medio Pago. Ver T.G. 'S1'</td>
            <td>Si Tipo de Documento es 'NA' ó 'ND' Ver T.G. 06</td>
            <td>Si Tipo de Documento es 'NC', 'NA' ó 'ND', incluye Serie y Número</td>
            <td>Si Tipo de Documento es 'NC', 'NA' ó 'ND'</td>
            <td>Si Tipo de Documento es 'NC', 'NA' ó 'ND'. Solo cuando el Tipo Documento de Referencia 'TK'</td>
            <td>Si Tipo de Documento es 'NC', 'NA' ó 'ND'</td>
            <td>Si Tipo de Documento es 'NC', 'NA' ó 'ND'</td>
            <td>Si la Cuenta Contable tiene Habilitado Documento Referencia 2 y  Tipo de Documento es 'TK'</td>
            <td>Si la Cuenta Contable teien Habilitado Documento Referencia 2 y  Tipo de Documento es 'TK'</td>
            <td>Si la Cuenta Contable tiene Habilitado Documento Referencia 2. Cuando Tipo de Documento es 'TK', consignar la fecha de emision del ticket</td>
            <td>Si la Cuenta Contable tiene configurada la Tasa:  Si es '1' ver T.G. 28 y '2' ver T.G. 29</td>
            <td>Si la Cuenta Contable tiene conf. en Tasa:  Si es '1' ver T.G. 28 y '2' ver T.G. 29. Debe estar entre >=0 y <=999.99</td>
            <td>Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99</td>
            <td>Si la Cuenta Contable tiene configurada la Tasa. Debe ser el importe total del documento y estar entre >=0 y <=99999999999.99</td>
            <td>Especificar solo si Tipo Conversión es 'F'. Se permite 'M' Compra y 'V' Venta.</td>
            <td>Especificar solo para comprobantes de compras con IGV sin derecho de crédito Fiscal. Se detalle solo en la cuenta 42xxxx</td>
        </tr>

        <tr>
            <td>Tamaño/Formato</td>
            <td>4 Caracteres</td>
            <td>6 Caracteres</td>
            <td>dd/mm/aaaa</td>
            <td>2 Caracteres</td>
            <td>40 Caracteres</td>
            <td>Numérico 11, 6</td>
            <td>1 Caracteres</td>
            <td>1 Caracteres</td>
            <td>dd/mm/aaaa</td>
            <td>12 Caracteres</td>
            <td>18 Caracteres</td>
            <td>6 Caracteres</td>
            <td>1 Carácter</td>
            <td>Numérico 14,2</td>
            <td>Numérico 14,2</td>
            <td>Numérico 14,2</td>
            <td>2 Caracteres</td>
            <td>20 Caracteres</td>
            <td>dd/mm/aaaa</td>
            <td>dd/mm/aaaa</td>
            <td>3 Caracteres</td>
            <td>30 Caracteres</td>
            <td>18 Caracteres</td>
            <td>8 Caracteres</td>
            <td>2 Caracteres</td>
            <td>20 Caracteres</td>
            <td>dd/mm/aaaa</td>
            <td>20 Caracteres</td>
            <td>Numérico 14,2</td>
            <td>Numérico 14,2</td>
            <td>'MQ'</td>
            <td>15 caracteres</td>
            <td>dd/mm/aaaa</td>
            <td>5 Caracteres</td>
            <td>Numérico 14,2</td>
            <td>Numérico 14,2</td>
            <td>Numérico 14,2</td>
            <td>1 Caracter</td>
            <td>Numérico 14,2</td>
        </tr> --}}
    </thead>

    <tbody> 
        @foreach($records as $row)
        <tr>
            @foreach ($row as $item)
                <td>{{ $item }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
