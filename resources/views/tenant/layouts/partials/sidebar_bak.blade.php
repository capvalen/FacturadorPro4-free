@php
    $path = explode('/', request()->path());
    $path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
    $path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
    $path[0] = ($path[0] === '')?'documents':$path[0];
@endphp

<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title">
            Menu
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html"
             data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="
                        nav-parent
                        {{ ($path[0] === 'documents')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'items')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'persons' && $path[1] === 'customers')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'summaries')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'voided')?'nav-active nav-expanded':'' }}
                        ">
                        <a class="nav-link" href="#">
                            <i class="fas fa-receipt" aria-hidden="true"></i>
                            <span>VENTAS</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('documents', $vc_modules))
                            <li class="{{ ($path[0] === 'documents' && $path[1] === 'create')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.documents.create')}}">
                                    Nuevo comprobante electrónico
                                </a>
                            </li>
                            @endif
                            @if(in_array('documents', $vc_modules))
                            <li class="{{ ($path[0] === 'documents' && $path[1] != 'create')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.documents.index')}}">
                                    Listado de comprobantes
                                </a>
                            </li>
                            @endif
                            @if(in_array('items', $vc_modules))
                            <li class="{{ ($path[0] === 'items')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.items.index')}}">
                                    Productos
                                </a>
                            </li>
                            @endif
                            @if(in_array('customers', $vc_modules))
                            <li class="{{ ($path[0] === 'persons' && $path[1] === 'customers')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.persons.index', ['type' => 'customers'])}}">
                                    Clientes
                                </a>
                            </li>
                            @endif
                            <li class="nav-parent
                                {{ ($path[0] === 'summaries')?'nav-active nav-expanded':'' }}
                                {{ ($path[0] === 'voided')?'nav-active nav-expanded':'' }}
                                ">
                                <a class="nav-link" href="#">
                                    Resúmenes y Anulaciones
                                </a>
                                <ul class="nav nav-children">

                                    @if(in_array('summaries', $vc_modules))
                                    <li class="{{ ($path[0] === 'summaries')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.summaries.index')}}">
                                            Resúmenes
                                        </a>
                                    </li>
                                    @endif

                                    @if(in_array('voided', $vc_modules))
                                    <li class="{{ ($path[0] === 'voided')?'nav-active':'' }}">
                                        <a class="nav-link" href="{{route('tenant.voided.index')}}">
                                            Anulaciones
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </li>
                            <li class="#">
                                <a class="nav-link" href="#">
                                    Ventas sin facturar (Pronto)
                                </a>
                            </li>
                            {{-- <p class="py-0 text-center my-0">
                                <span><small class="text-muted">Facturas | Notas <small>(crédito y débito)</small> | Boletas | Anulaciones</small></span>
                            </p> --}}
                        </ul>
                    </li>

                    <li class="
                        nav-parent
                        {{ ($path[0] === 'purchases')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'persons' && $path[1] === 'suppliers')?'nav-active nav-expanded':'' }}
                        ">
                        <a class="nav-link" href="#">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            <span>Compras</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('purchases', $vc_modules))
                            <li class="{{ ($path[0] === 'purchases' && $path[1] != 'create')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.purchases.index')}}">
                                    Listado
                                </a>
                            </li>
                            @endif
                            @if(in_array('purchases', $vc_modules))
                            <li class="{{ ($path[0] === 'purchases' && $path[1] === 'create')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.purchases.create')}}">
                                    Nuevo
                                </a>
                            </li>
                            @endif
                            @if(in_array('suppliers', $vc_modules))
                            <li class="{{ ($path[0] === 'persons' && $path[1] === 'suppliers')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.persons.index', ['type' => 'suppliers'])}}">
                                    Proveedores
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-parent {{ in_array($path[0], ['users', 'establishments'])?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <span>Usuarios/Locales & Series</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('users', $vc_modules))
                            <li class="{{ ($path[0] === 'users')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.users.index')}}">
                                    Usuarios
                                </a>
                            </li>
                            @endif
                            @if(in_array('establishments', $vc_modules))
                            <li class="{{ ($path[0] === 'establishments')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.establishments.index')}}">
                                    Establecimientos
                                </a>
                            </li>
                                @endif
                        </ul>
                    </li>

                    <li class="
                        nav-parent
                        {{ ($path[0] === 'retentions')?'nav-active nav-expanded':'' }}
                        {{ ($path[0] === 'dispatches')?'nav-active nav-expanded':'' }}
                        ">
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <span>Comprobantes avanzados</span>
                        </a>
                        <ul class="nav nav-children" style="">

                            {{--<li class="#">--}}
                                {{--<a class="nav-link" href="#">--}}
                                    {{--Notas de débito--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="#">--}}
                                {{--<a class="nav-link" href="#">--}}
                                    {{--Notas de crébito--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{-- @if(in_array('summaries', $vc_modules))
                            <li class="{{ ($path[0] === 'summaries')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.summaries.index')}}">
                                    Resúmenes
                                </a>
                            </li>
                            @endif --}}
                            {{-- @if(in_array('voided', $vc_modules))
                            <li class="{{ ($path[0] === 'voided')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.voided.index')}}">
                                    Anulaciones
                                </a>
                            </li>
                            @endif --}}
                            @if(in_array('retentions', $vc_modules))
                            <li class="{{ ($path[0] === 'retentions')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.retentions.index')}}">
                                    Retenciones
                                </a>
                            </li>
                            @endif
                            @if(in_array('dispatches', $vc_modules))
                            <li class="{{ ($path[0] === 'dispatches')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.dispatches.index')}}">
                                    Guías de remisión
                                </a>
                            </li>
                            @endif
                            <li class="#">
                                <a class="nav-link" href="#">
                                    Percepciones (Pronto)
                                </a>
                            </li>

                        </ul>
                    </li>

                    {{--<li class="--}}
                        {{--nav-parent--}}
                        {{--">--}}
                        {{--<a class="nav-link" href="#">--}}
                            {{--<i class="fas fa-file-alt" aria-hidden="true"></i>--}}
                            {{--<span>Inventario</span>--}}
                        {{--</a>--}}
                        {{--<ul class="nav nav-children" style="">--}}

                            {{--<li class="#">--}}
                                {{--<a class="nav-link" href="#">--}}
                                    {{--Productos--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="{{(($path[0] === 'reports') && ($path[1] != 'inventories')) ? 'nav-active' : ''}}">--}}
                                {{--<a class="nav-link" href="{{route('tenant.reports.inventories.index')}}">--}}
                                    {{--Valor actual del inventario--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li class="{{(($path[0] === 'reports') && ($path[1] != 'kardex')) ? 'nav-active' : ''}}">--}}
                                {{--<a class="nav-link" href="{{route('tenant.reports.kardex.index')}}">--}}
                                    {{--Kardex--}}
                                {{--</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                    <li class="nav-parent {{  (($path[0] === 'reports') && in_array($path[1], ['', 'purchases', 'kardex', 'inventories'])) ? 'nav-active nav-expanded' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-area" aria-hidden="true"></i>
                            <span>Reportes</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            <li class="{{(($path[0] === 'reports') && ($path[1] === 'purchases')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.reports.purchases.index')}}">
                                    Compras
                                </a>
                            </li>
                            {{--<li class="#">--}}
                                {{--<a class="nav-link" href="#">--}}
                                    {{--Ventas--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            <li class="{{(($path[0] === 'reports') && ($path[1] === '')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.reports.index')}}">
                                    Ventas
                                </a>
                            </li>
                            <li class="{{(($path[0] === 'reports') && ($path[1] === 'kardex')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.reports.kardex.index')}}">
                                    Kardex
                                </a>
                            </li>
                            <li class="{{(($path[0] === 'reports') && ($path[1] == 'inventories')) ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('tenant.reports.inventories.index')}}">
                                    Inventarios
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent {{ in_array($path[0], ['companies', 'catalogs', 'advanced'])?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cogs" aria-hidden="true"></i>
                            <span>Configuracion</span>
                        </a>
                        <ul class="nav nav-children" style="">
                            @if(in_array('companies', $vc_modules))
                            <li class="{{ ($path[0] === 'companies')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.companies.create')}}">
                                    Empresa
                                </a>
                            </li>
                            @endif
                            @if(in_array('catalogs', $vc_modules))
                            <li class="{{ ($path[0] === 'catalogs')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.catalogs.index')}}">
                                    Catálogos
                                </a>
                            </li>
                            @endif
                            @if(in_array('advanced', $vc_modules))
                            <li class="{{ ($path[0] === 'advanced')?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.advanced.index')}}">
                                    Avanzado
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>



                    {{--<li class="{{ ($path[0] === 'perceptions')?'nav-active':'' }}">--}}
                        {{--<a class="nav-link" href="{{route('tenant.perceptions.index')}}">--}}
                            {{--<i class="fas fa-receipt"></i><span>Percepciones</span>--}}
                        {{--</a>--}}
                    {{--</li --}}

                </ul>
            </nav>
        </div>
        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>