@extends('tenant.layouts.app')

@section('content')
<div class="page-header pr-0">
    <h2>
        <a href="/dashboard">
            <i class="fas fa-home"></i>
        </a>
    </h2>
    <ol class="breadcrumbs">
        <li class="active">
            <span>Dashboard</span>
        </li>
        <li>
            <span class="text-muted">Reportes</span>
        </li>
    </ol>
</div>

<div class="row">
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">General</h6>
                <ul class="card-report-links">
                    @if(auth()->user()->type != 'integrator')
                    <li>
                        <a href="{{ url('list-banks') }}">Listado de bancos</a>
                    </li>
                    <li>
                        <a href="{{url('list-bank-accounts')}}">Listado de cuentas bancarias</a>
                    </li>
                    <li>
                        <a href="{{url('list-currencies')}}">Lista de monedas</a>
                    </li>
                    <li>
                        <a href="{{url('list-cards')}}">Listado de tarjetas</a>
                    </li>
                    <li>
                        <a href="{{url('list-platforms')}}">Plataformas</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Empresa</h6>
                <ul class="card-report-links">
                    <li>
                        <a href="{{route('tenant.companies.create')}}">Empresa</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.bussiness_turns.index')}}">Giro de negocio</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.index')}}">Avanzado</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">SUNAT</h6>
                <ul class="card-report-links">
                    @if(auth()->user()->type != 'integrator')
                    <li>
                        <a href="{{url('list-attributes')}}">Listado de Atributos</a>
                    </li>
                    <li>
                        <a href="{{url('list-detractions')}}">Listado de tipos de detracciones</a>
                    </li>
                    <li>
                        <a href="{{url('list-units')}}">Listado de unidades</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Ingresos/Egresos</h6>
                <ul class="card-report-links">
                    @if(auth()->user()->type != 'integrator')
                    <li>
                        <a href="{{url('list-payment-methods')}}">Métodos de pago - ingreso / gastos</a>
                    </li>
                    <li>
                        <a href="{{url('list-incomes')}}">Motivos de ingresos / Gastos</a>
                    </li>
                    <li>
                        <a href="{{url('list-payments')}}">Listado de métodos de pago</a>
                    </li>
                    @endif
                    @if(auth()->user()->type != 'integrator')
                    <li>
                        <a href="{{url('list-vouchers-type')}}">Tipos de comprobantes INGRESOS Y GASTOS</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Plantillas PDF</h6>
                <ul class="card-report-links">
                    <li>
                        <a href="{{route('tenant.advanced.pdf_templates')}}">PDF</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.pdf_guide_templates')}}">Guía de remisión</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.pdf_preprinted_templates')}}">Pre Impresos</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Avanzado</h6>
                <ul class="card-report-links">
                    @if(auth()->user()->type != 'integrator' && $vc_company->soap_type_id != '03')
                    <li>
                        <a href="{{route('tenant.tasks.index')}}">Tareas programadas</a>
                    </li>
                    @endif
                    @if($vc_company->soap_type_id != '03')
                    <li>
                        <a href="{{route('tenant.offline_configurations.index')}}">Modo offline</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.series_configurations.index')}}">Numeración de facturación</a>
                    </li>
                    @endif
                    <li>
                        <a href="{{route('tenant.company_accounts.create')}}">Avanzado - Contable</a>
                    </li>
                    @if(auth()->user()->type != 'integrator' && $vc_company->soap_type_id != '03')
                    <li>
                        <a href="{{route('tenant.inventories.configuration.index')}}">Inventarios</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @if (! $useLoginGlobal)
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Visual</h6>
                <ul class="card-report-links">
                    {{-- @if(auth()->user()->type != 'integrator')
                    <li class="{{($path[0] === 'catalogs') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('tenant.catalogs.index')}}">
                            Catálogos
                        </a>
                    </li>
                    @endif --}}
                    <li>
                        <a href="{{route('tenant.login_page')}}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
