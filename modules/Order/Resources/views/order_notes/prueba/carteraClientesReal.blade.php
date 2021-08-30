<div class="container">
    <h2>CARTERA DE CLIENTES de:  {{ $vendedor}}, día {{ $dia }}</h2>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Departamento</th>
                        <th>Provincia</th>
                        <th>Distrito</th>
                        <th>Dirección</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $item)
                        <tr>
                            <td>{{ $item["number"] }}</td>
                            <td>{{ $item["name"] }}</td>
                            <td>{{ $item["department"]->description }}</td>
                            <td>{{ $item["province"]->description }}</td>
                            <td>{{ $item["district"]->description }}</td>
                            <td>{{ $item["address"] }}</td>
                            <td>{{ $item["email"] }}</td>
                            <td>{{ $item["telephone"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>