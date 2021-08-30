@extends('tenant.layouts.app')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>

</style>
@section('content')
<div class="container">
    <h2>REPORTE DE PEDIDOS</h2>
    <div class="card">
        <div class="card-header">
            <h4>Seleccionar fecha</h4>
            <hr>
            
            <form method="get" action="{{ route('tenant.order_notes.pedidos') }}">
                <input type="date" name="fecha" required>
                <button type="submit">Ver Pedidos</button>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
<div class="container">
    <h2>REPORTE DE PEDIDOS y VENDEDOR</h2>
    <div class="card">
        <div class="card-header">
            <form method="get" action="{{ route('tenant.order_notes.pedidos_y_vendedor') }}">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" required>
                    </div>
                    <div class="form-group col-md-6">
                        @foreach ($sellers as $key => $item)
                            <input type="checkbox" id="{{$item["id"]}}" name="{{$item["id"]}}" value="{{$item["id"]}}" style="position: '' !important">
                            <label>{{$item["name"]}}</label><br>
                        @endforeach
                    </div>
                </div>
                <button type="submit">Ver Pedidos y vendedor</button>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
<div class="container">
    <h2>AVANZE DE VENTAS: VENDEDOR x PRODUCTO</h2>
    <div class="card">
        <div class="card-header">            
            <form class="form-horizontal" method="get" action="{{ route('tenant.order_notes.reporte_vendedor') }}">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="vendedor" style="display: block">Vendedor</label>
                        <select name="user_id" id="">
                            @foreach ($sellers as $key => $item)
                            <option value="{{$item["id"]}}">{{$item["name"]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_inicial">Fecha inicial</label>
                        <input type="date" name="fecha_inicial" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="fecha_final">Fecha Final</label>
                        <input type="date" name="fecha_final" required>
                    </div>
                </div>
                <button type="submit">Ver Avance de Ventas</button>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
<div class="container">
    <h2>TOTAL DE CLIENTES ATENDIDOS X VENDEDOR</h2>
    <div class="card">
        <div class="card-header">
            <form method="get" action="{{ route('tenant.order_notes.cartera_clientes') }}">
                <label for="vendedor" style="display: block">Vendedor</label>
                <select name="user_id" id="">
                    @foreach ($sellers as $key => $item)
                    <option value="{{$item["id"]}}">{{$item["name"]}}</option>
                    @endforeach
                </select>
                <button type="submit">Ver Clientes</button>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
<div class="container">
    <h2>CLIENTES ATENDIDOS POR DIA</h2>
    <div class="card">
        <div class="card-header">
            <form method="get" action="{{ route('tenant.order_notes.clientes_visitados') }}">
                <div class="row">
                    <div>
                        <label for="vendedor" style="display: block">Vendedor</label>
                        <select name="user_id" id="">
                            @foreach ($sellers as $key => $item)
                            <option value="{{$item["id"]}}">{{$item["name"]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="form-group col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" name="fecha" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit">Ver Clientes atendidos</button>
                    </div>
                </div>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
<div class="container">
    <h2>CARTERA DE CLIENTES X DIA</h2>
    <div class="card">
        <div class="card-header">
            <form method="get" action="{{ route('tenant.order_notes.cartera_clientes_real') }}">
                <div class="row">
                    <div>
                        <label for="vendedor" style="display: block">Vendedor</label>
                        <select name="name" id="">
                            @foreach ($sellers as $key => $item)
                            <option value="{{$item["name"]}}">{{$item["name"]}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dia" style="display: block">Día</label>
                        <select name="dia" id="dia">
                            <option value="lunes">Lunes</option>
                            <option value="martes">Martes</option>
                            <option value="miercoles">Miercoles</option>
                            <option value="jueves">Jueves</option>
                            <option value="viernes">Viernes</option>
                            <option value="sabado">Sabado</option>
                            <option value="domingo">Domingo</option>
                            <option value="todos">Todos</option>    
                        </select>
                    </div>
                    <div>
                        <button type="submit">Ver Cartera</button>
                    </div>
                </div>
            </form>
            @if($errors->any())
                <h4>{{$errors->first()}}</h4>
            @endif
        </div>
    </div>
</div>
@endsection