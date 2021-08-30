<style>
    .btn-logout {
        font-size: 25px;
        margin-left: 6%;
    }

</style>

<div class="dropdown cart-dropdown" style="margin-left: 9px;">

    @guest
    <div class="header-contact">
        <a class="login-link" href="{{route('tenant_ecommerce_login')}}"><strong style="font-size: 16px;">LOG
                IN</strong></a>
    </div>
    @else
    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        data-display="static">
        <i class="icon-user fa-2x text-white"></i> </a>
    <div class="dropdown-menu">
        <div class="dropdownmenu-wrapper">

            <div class="dropdown-cart-total d-flex justify-content-center" >
                <span>{{ Auth::user()->email }} </span>
                <a href="#" role="menuitem" class="btn-logout" data-toggle="tooltip" data-placement="bottom"
                    title="Cerrar Session" onclick="event.preventDefault(); logout();">
                    <i class="fas fa-power-off"></i>
                </a>

            </div>

        </div>
    </div>

    @endguest

</div>
