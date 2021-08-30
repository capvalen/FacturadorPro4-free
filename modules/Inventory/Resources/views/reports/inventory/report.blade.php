<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if($format === 'pdf')
    <meta http-equiv="Content-Type" content="application/pdf; charset=utf-8" />
    @else
    <meta http-equiv="Content-Type"
          content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8"/>
    @endif
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inventario</title>
    <style>
        html {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        .title {
            font-weight: 500;
            text-align: center;
            font-size: 24px;
        }
        .label {
            width: 120px;
            font-weight: 500;
            font-family: sans-serif;
        }
        .table-records {
            margin-top: 24px;
        }
        .table-records tr th {
            font-weight: bold;
            background: #0088cc;
            color: white;
        }
        .table-records tr th,
        .table-records tr td {
            border: 1px solid #000;
            font-size: 9px;
        }
    </style>
</head>
<body>
<table style="width: 100%">
    <tr>
        <td colspan="13" class="title"><strong>Reporte Inventario</strong></td>
    </tr>
    <tr>
        <td colspan="2" class="label">Empresa:</td>
        <td>{{$company->name}}</td>
    </tr>
    <tr>
        <td colspan="2" class="label">RUC:</td>
        <td align="left">{{$company->number}}</td>
    </tr>
    <tr>
        <td colspan="2" class="label">Establecimiento:</td>
        <td>{{$establishment->address}} - {{$establishment->department->description}}
            - {{$establishment->district->description}}</td>
    </tr>
    <tr>
        <td colspan="2" class="label">Fecha:</td>
        <td>{{ date('d/m/Y')}}</td>
    </tr>
</table>
<table style="width: 100%" class="table-records">
    <thead>
    <tr>
        <th><strong>#</strong></th>
        <th><strong>Cod. de barras</strong></th>
        <th><strong>Cod. Interno</strong></th>
        <th><strong>Descripción</strong></th>
        <th><strong>Categoria</strong></th>
        <th align="right"><strong>Stock mínimo</strong></th>
        <th align="right"><strong>Stock actual</strong></th>
        <th align="right"><strong>Costo</strong></th>
        <th align="right"><strong>Costo Total</strong></th>
        <th align="right"><strong>Precio de venta</strong></th>
        <th><strong>Marca</strong></th>
        <th><strong>F. vencimiento</strong></th>
        <th><strong>Almacén</strong></th>
    </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
    @endphp
    @foreach($records as $key => $row)
        @php
            $total_line = $row['stock'] * $row['purchase_unit_price'];
            $total = $total + $total_line;
        @endphp
        <tr>
            <td>{{ $loop->iteration}}</td>
            <td>{{ $row['barcode'] }}</td>
            <td>{{ $row['internal_id'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td>{{ $row['item_category_name'] }}</td>
            <td align="right">{{ $row['stock_min'] }}</td>
            <td align="right">{{ $row['stock'] }}</td>
            <td align="right">{{ $row['purchase_unit_price'] }}</td>
            <td align="right">{{ $total_line }}</td>
            <td align="right">{{ $row['sale_unit_price'] }}</td>
            <td>{{ $row['brand_name'] }}</td>
            <td>{{ $row['date_of_due'] }}</td>
            <td>{{ $row['warehouse_name'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="8" align="right">Costo Total de Inventario</td>
        <td align="right">{{ $total }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>
</body>
</html>
