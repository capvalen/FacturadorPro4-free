<div class="container">
    <h2>REPORTE DE PRODUCTOS VENDEDIDOS X VENDEDOR: del  {{ $fecha_inicial }}  al  {{$fecha_final}}</h2>
    <h3>Vendedor: {{$vendedor}} con un total de: {{$venta_total_vendedor}}</h3>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>{{ $item["producto"] }}</td>
                            <td>{{ $item["unit_price"] }}</td>
                            <td>{{ $item["quantity"] }}</td>
                            <td>{{ $item["total"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>