@php
    $path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
@endphp

<html>
  <head>
    <link href="{{ $path_style }}" rel="stylesheet" />
  </head>
<body>
<table class="full-width">
    <tr>
        <td class="text-center">{{ $company->name }}</td>
    </tr>
    <tr>
        <td class="text-center">{{ 'RUC '.$company->number }}</td>
    </tr>
    <tr>
        <td class="text-center">
            {{ ($establishment->address !== '-')? $establishment->address : '' }}
            {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
            {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
            {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
        </td>
    </tr>
    <tr>
        <td class="text-center">{{ ($establishment->email !== '-')? $establishment->email : '' }}</td>
    </tr>
    <tr>
      <td class="text-center pb-3 border-top"><b>Comprobante de pedido</b></td>
    </tr>
    <tr>
      <td class="text-center pb-3 border-bottom"><h3>{{ str_pad($records->id, 6, "0", STR_PAD_LEFT) }}</h3></td>
    </tr>
</table>

<table class="full-width">
  <tr>
    <td>F. Emisión:</td>
    <td>{{ $records->created_at }}</td>
  </tr>
  <tr>
    <td>Medio Pago:</td>
    <td>{{ $records->reference_payment }}</td>
  </tr>
  <tr>
    <td>Cliente:</td>
    <td>{{ $customer->apellidos_y_nombres_o_razon_social }}</td>
  </tr>
  <tr>
    <td>Correo:</td>
    <td>{{ $customer->correo_electronico }}:</td>
  </tr>
  <tr>
    <td>Teléfono:</td>
    <td>{{ $customer->telefono }}</td>
  </tr>
  @if ($customer->direccion !== '')
  <tr>
    <td>Dirección:</td>
    <td>{{ $customer->direccion }}</td>
  </tr>
  @endif
</table>

<table class="">
  <thead class="">
    <tr>
      <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
      <th class="border-top-bottom desc-9 text-left">CANT.</th>
      <th class="border-top-bottom desc-9 text-left">MONEDA</th>
      <th class="border-top-bottom desc-9 text-left">P.UNIT</th>
      <th class="border-top-bottom desc-9 text-left">TOTAL</th>
    </tr>
  </thead>
  <tbody>
    @foreach($records->items as $row)
    <tr>
      <td>{{ $row->description }}</td>
      <td>{{ $row->cantidad }}</td>
      <td>{{ $row->currency_type_id }}</td>
      <td>{{ $row->currency_type->symbol }}{{ $row->sale_unit_price }}</td>
      <td>S/ {{ $row->sub_total }}</td>
    </tr>
    @endforeach
    <tr><td></td></tr>
    <tr>
      <td colspan="4" class="text-right font-bold desc">TOTAL A PAGAR: S/ {{ number_format($records->total, 2) }}</td>
    </tr>
  </tbody>
</table>

</body>
</html>
