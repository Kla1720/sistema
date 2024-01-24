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
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TDOC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SDOC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NDOC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">FECH</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">RUC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NOMB</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TBAS</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">BASE</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">INAF</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">ISC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">IGV</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">PERCEP</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TOTAL</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TREF</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SREF</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">NREF</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">FREF</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">MON</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">TC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">GLOSA</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">SERV</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">CJFEC</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">CJCTA</th>
                <th style="background-color: #0070c0; font-weight: bold; font-size: 9px; color: #FFFFFF; text-align: center;">CJIMP</th>
            </tr>
        </thead>
        @foreach($records as $row)
        <tr>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tipdoc'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['serie'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['numero'] }}</td>
            <td style="min-width: 160px; font-size: 9px;">{{ $row['fecfac'] }}</td>
            <td style="min-width: 190px; font-size: 9px;">{{ $row['nro_ruc'] }}</td>
            <td style="min-width: 500px; font-size: 9px;">{{ $row['nombre'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['t_base'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_vta'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_exo'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['isc'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_igv'] }}</td>
            <td style="min-width: 100px; font-size: 9px;"></td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_tot'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tref'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['sref'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['nref'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['fref'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tipmon'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['tip_cam'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['glosa'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">{{-- serv --}}</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['fecfac'] }}</td>
            <td style="min-width: 100px; font-size: 9px;">101</td>
            <td style="min-width: 100px; font-size: 9px;">{{ $row['imp_tot'] }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
