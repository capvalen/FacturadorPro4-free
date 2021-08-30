<div class="container">
    <h2>CARTERA DE CLIENTES de:  {{ $vendedor}}</h2>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Distrito</th>
                        <th>Provincia</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>{{ $item["number"] }}</td>
                            <td>{{ $item["person_name"] }}</td>
                            <td>{{ $item["address"] }}</td>
                            <td>{{ $item["distrito"] }}</td>
                            <td>{{ $item["provincia"] }}</td>
                            <td>{{ $item["email"] }}</td>
                            <td>{{ $item["telephone"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>