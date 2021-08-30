<header class="header">
    <div class="logo-container">
        {{-- <a href="{{route('tenant.dashboard.index')}}" class="logo pt-2 pt-md-0">

            @if($vc_company->logo)
                <img src="{{ asset('storage/uploads/logos/'.$vc_company->logo) }}" alt="Logo" />
            @else
                <img src="{{asset('logo/700x300.jpg')}}" alt="Logo" />
            @endif
        </a> --}}
        <div class="sidebar-toggle" data-toggle-class="sidebar-left-collapsed" data-target="html"
             data-fire-event="sidebar-left-toggle">
             {{-- <img src="{{ asset('images/disc.svg') }}" alt="Sidebar toggle" class="img-fluid" width="20"> --}}
            <i class="fas fa-bullseye" aria-label="Toggle sidebar"></i>
        </div>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>

        <a class="topbar-links" href="{{ route('tenant.documents.create') }}" title="Nueva factura" data-toggle="tooltip" data-placement="bottom">
            <i class="fas fa-fw fa-plus" aria-hidden="true"></i>
            <span>FA</span>
        </a>
        <a class="topbar-links" href="{{ in_array('pos', $vc_modules) ? route('tenant.pos.index') : '#' }}" title="POS" data-toggle="tooltip" data-placement="bottom">
            <i class="fas fa-fw fa-plus" aria-hidden="true"></i>
            <span>POS</span>
        </a>
        <a class="topbar-links" href="{{ in_array('configuration', $vc_modules) ? route('tenant.companies.create') : '#' }}" title="Mi empresa" data-toggle="tooltip" data-placement="bottom">
            <i class="fas fa-fw fa-plus" aria-hidden="true"></i>
            <span>ME</span>
        </a>
        <a class="topbar-links" href="{{ in_array('establishments', $vc_modules) ? route('tenant.establishments.index') : '#' }}" title="Nuevo establecimiento" data-toggle="tooltip" data-placement="bottom">
            <i class="fas fa-fw fa-plus" aria-hidden="true"></i>
            <span>ES</span>
        </a>

    </div>
    <div class="header-right">

        <ul class="notifications mx-2">
            @if($vc_company->soap_type_id == "01")
                <li>
                    <a href="@if(in_array('configuration', $vc_modules)){{route('tenant.companies.create')}}@else # @endif" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="SUNAT: ENTORNO DE DEMOSTRACIÓN, pulse para ir a configuración">
                        <i class="fas fa-2x fa-toggle-off mr-2" style="font-size: 20px"></i>
                        <span>DEMO</span>
                    </a>
                </li>
            @elseif($vc_company->soap_type_id == "02")
                <li>
                    <a href="@if(in_array('configuration', $vc_modules)){{route('tenant.companies.create')}}@else # @endif" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="SUNAT: ENTORNO DE PRODUCCIÓN, pulse para ir a configuración">
                        <i class="text-success fas fa-2x fa-toggle-on mr-2" style="font-size: 20px; color: #28a745 !important"></i>
                        <span>PROD</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="@if(in_array('configuration', $vc_modules)){{route('tenant.companies.create')}}@else # @endif" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="INTERNO: ENTORNO DE PRODUCCIÓN, pulse para ir a configuración">
                        <i class="text-info fas fa-2x fa-toggle-on mr-2" style="font-size: 20px; color: #398bf7!important;"></i>
                        <span>INT</span>
                    </a>
                </li>
            @endif
        </ul>

        <span class="separator"></span>
        <ul class="notifications">
            <li>
                <a href="{{ route('settings.change_mode') }}" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="Modo noche">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </a>
            </li>
        </ul>
        <span class="separator"></span>
        <ul class="notifications">
            <li>
                <a href="{{ route('tenant_orders_index') }}" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="Pedidos pendientes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="badge badge-pill badge-info badge-up cart-item-count">{{ $vc_orders }}</span>
                </a>
            </li>
        </ul>
        @if($vc_document > 0)
        <span class="separator"></span>
        <ul class="notifications">
            <li>
                <a href="{{route('tenant.documents.not_sent')}}" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="Comprobantes pendientes de envío">
                    <i class="far fa-bell text-secondary"></i>
                    <span class="badge badge-pill badge-danger badge-up cart-item-count">{{ $vc_document }}</span>
                </a>
            </li>
        </ul>
        @endif

        @if($vc_document_regularize_shipping > 0)
        <span class="separator"></span>
        <ul class="notifications">
            <li>
                <a href="{{route('tenant.documents.regularize_shipping')}}" class="notification-icon text-secondary" data-toggle="tooltip" data-placement="bottom" title="Comprobantes pendientes de rectificación">
                    <i class="fas fa-exclamation-triangle text-secondary"></i>
                    <span class="badge badge-pill badge-danger badge-up cart-item-count">{{ $vc_document_regularize_shipping }}</span>
                </a>
            </li>
        </ul>
        @endif

        <span class="separator"></span>
        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <div class="profile-info" data-lock-name="{{ $vc_user->email }}" data-lock-email="{{ $vc_user->email }}">
                    <span class="name">{{ $vc_user->name }}</span>
                    <span class="role">{{ $vc_user->email }}</span>
                </div>
                <figure class="profile-picture">
                    {{-- <img src="{{asset('img/%21logged-user.jpg')}}" alt="Profile" class="rounded-circle" data-lock-picture="img/%21logged-user.jpg" /> --}}
                    <div class="border rounded-circle text-center" style="width: 25px;"><i class="fas fa-user"></i></div>
                </figure>
                {{-- <i class="fa custom-caret"></i> --}}
            </a>
            <div class="dropdown-menu">
                <ul class="list-unstyled mb-0">
                    {{-- <li class="divider"></li> --}}
                    <li>
                        {{--<a role="menuitem" href="#"><i class="fas fa-user"></i> Perfil</a>--}}
                        <a role="menuitem" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-power-off"></i> @lang('app.buttons.logout')
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
{{--
<div class="container d-none d-sm-block">
    <div id="switcher-top" class="d-flex justify-content-center switcher-hover">
        <span class="text-white py-0 px-5 text-center"><i class="fas fa-plus fa-fw"></i>Acceso Rápido</span>
    </div>
</div>
<div class="container d-none d-sm-block">
    <div id="switcher-list" class="d-flex justify-content-center switcher-hover">
        <div class="row">
            <div class="px-3"><a class="py-3" href="{{ route('tenant.documents.create') }}"><i class="fas fa-fw fa-file-invoice" aria-hidden="true"></i> Nuevo Comprobante</a></div>
            <div class="px-3"><a class="py-3" href="{{ in_array('pos', $vc_modules) ? route('tenant.pos.index') : '#' }}"><i class="fas fa-fw fa-cash-register" aria-hidden="true"></i> POS</a></div>
            <div style="min-width: 220px;"></div>
            <div class="px-3"><a class="py-3" href="{{ in_array('configuration', $vc_modules) ? route('tenant.companies.create') : '#' }}"><i class="fas fa-fw fa-industry" aria-hidden="true"></i> Empresa</a></div>
            <div class="px-3"><a class="py-3" href="{{ in_array('establishments', $vc_modules) ? route('tenant.establishments.index') : '#' }}"><i class="fas fa-fw fa-warehouse" aria-hidden="true"></i> Establecimientos</a></div>
        </div>
    </div>
</div> --}}
