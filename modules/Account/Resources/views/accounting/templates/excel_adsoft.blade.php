<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="29" style="text-align: center; font-size: 10px; font-weight: bold">REGISTRO DE VENTAS</th>
            </tr>
            <tr>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SERIE</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NUMERO</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">FECFAC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">FECVEN</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NRO_RUC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NOMBRE</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TIPDOC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TIPMON</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">DETRAC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_VTA</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">ISC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">ICBPER</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_INA</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_EXO</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_EXP</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">RECARGO</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_IGV</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IMP_TOT</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">ST</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SER_DQM</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NRO_DQM</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">FEC_DQM</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TIP_DQM</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SERIE_FIN</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NUMERO_FIN</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NRO_DNI</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">PASAPORTE</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">CTA_VTA</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TIP_CAM</th>
            </tr>
        </thead>
        @foreach($records as $row)
        <tr>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['serie'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['numero'] }}</td>
            <td style="min-width: 160px; font-size: 9px;">{{ $row['fecfac'] }}</td>
            <td style="min-width: 160px; font-size: 9px;">{{ $row['fecven'] }}</td>
            <td style="min-width: 190px; font-size: 9px;">{{ $row['nro_ruc'] }}</td>
            <td style="min-width: 500px; font-size: 9px;">{{ $row['nombre'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tipdoc'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tipmon'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['detrac'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_vta'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['isc'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['icbper'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_ina'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_exo'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_exp'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['recargo'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_igv'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_tot'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['st'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['ser_dqm'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['nro_dqm'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['fec_dqm'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tip_dqm'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['serie_fin'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['numero_fin'] }}</td>
            <td style="min-width: 150px; font-size: 9px;">{{ $row['nro_dni'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['pasaporte'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['cta_vta'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tip_cam'] }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
