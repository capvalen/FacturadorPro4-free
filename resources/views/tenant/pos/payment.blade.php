@extends('tenant.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}"/>
@endpush

@section('content')

    <header class="page-header pr-0">
        <h2 class="text-sm">POS</h2>
        <div class="right-wrapper pull-right">
            <h2 class="text-sm">USUARIO</h2>
        </div>
    </header>
    <div class="row col-lg-12 m-0 p-0">
        <div class="col-lg-4 col-md-6 bg-white m-0 p-0" style="height: calc(100vh - 110px)">
            <div class="h-75 bg-light" style="overflow-y: auto">
                <div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div><div class="row py-1 border-bottom m-0 p-0">
                    <div class="col-2 p-r-0 m-l-2">
                        <p class="font-weight-semibold m-b-0">5</p> 
                    </div>
                    <div class="col-6 px-0">
                        <p class="m-b-0">Descripción del producto</p>
                        <p class="text-muted m-b-0"><small>Descuento 2%</small></p>
                    </div>
                    <div class="col-4 p-l-0">
                        <p class="font-weight-semibold m-b-0">S/ 240.00</p>
                    </div>
                </div>
            </div>
            <div class="h-25 bg-info" style="overflow-y: auto">
                <div class="row m-0 p-0 bg-white">
                    <div class="col-sm-6 py-1">
                        <p class="font-weight-semibold mb-0">SUBTOTAL</p>
                    </div>
                    <div class="col-sm-6 py-1 text-right">
                        <p class="font-weight-semibold mb-0">S/ 458.00</p>
                    </div>
                </div>
                <div class="row m-0 p-0 bg-white">
                    <div class="col-sm-6 py-1">
                        <p class="font-weight-semibold mb-0">DESCUENTO</p>
                    </div>
                    <div class="col-sm-6 py-1 text-right">
                        <p class="font-weight-semibold mb-0">S/ 4.00</p>
                    </div>
                </div>
                <div class="row m-0 p-0 bg-white">
                    <div class="col-sm-6 py-1">
                        <p class="font-weight-semibold mb-0">IGV</p>
                    </div>
                    <div class="col-sm-6 py-1 text-right">
                        <p class="font-weight-semibold mb-0">S/ 0.00</p>
                    </div>
                </div>
                <div class="row m-0 p-0 ">
                    <div class="col-sm-6 py-2">
                        <p class="font-weight-semibold mb-0 text-white">TOTAL</p>
                    </div>
                    <div class="col-sm-6 py-2 text-right">
                        <p class="font-weight-semibold mb-0 text-white">S/ 454.00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-6 px-4 pt-3 hyo">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-default">
                        <div class="card-body text-center">
                                <p class="my-0"><small>Monto a cobrar</small></p>
                                <h1 class="mb-2 mt-0">S/. 454.00</h1>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card card-default">
                        <div class="card-body">
                            <p class="text-center">Método de Pago</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only"></span>
                                    </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Efectivo</a>
                                    <a class="dropdown-item" href="#">Efectivo dólares</a>
                                </div>
                                </div>
                                <input type="text" class="form-control" aria-label="Text input with segmented dropdown button">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-6">
                            <button class="btn btn-block btn-primary">PAGAR</button>
                        </div>
                        <div class="col-lg-6">
                            <button class="btn btn-block btn-danger">CANCELAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush