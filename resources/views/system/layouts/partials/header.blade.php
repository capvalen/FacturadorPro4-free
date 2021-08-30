<header class="header" style="left:0;">
    <div class="logo-container">
        <a href="{{route('system.dashboard')}}" class="logo pt-2 pt-md-0">
            @if (file_exists(public_path('theme/logo.svg')))
                <img class="uk-logo-inverse" width="100" height="auto" src="{{asset('theme/logo.svg')}}" alt="Logo"/>
            @else
                <i class="fa fa-circle fa-3x"></i>
            @endif
        </a>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <!-- start: search & user box -->
    <div class="header-right">
        <span class="separator"></span>
        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <figure class="profile-picture">
                    {{-- <img src="{{asset('img/%21logged-user.jpg')}}" alt="Joseph Doe" class="rounded-circle" data-lock-picture="img/%21logged-user.jpg" /> --}}
                    <div class="border rounded-circle text-center" style="width: 25px;"><i class="fas fa-user"></i></div>
                </figure>
                <div class="profile-info" data-lock-name="{{ \Auth::getUser()->email }}" data-lock-email="{{ \Auth::getUser()->email }}">
                    <span class="name">{{ \Auth::getUser()->name }}</span>
                    <span class="role">{{ \Auth::getUser()->role }}</span>
                </div>
                <i class="fa custom-caret"></i>
            </a>
            <div class="dropdown-menu">
                <ul class="list-unstyled mb-2">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" href="{{ route('system.users.create') }}"><i class="fas fa-user"></i> Perfil</a>
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
    <!-- end: search & user box -->
</header>