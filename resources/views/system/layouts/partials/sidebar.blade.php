@php
    $path = explode('/', request()->path());
    $path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
    $path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
    $path[0] = ($path[0] === '')?'documents':$path[0];
@endphp
<aside id="sidebar-left" class="sidebar-left mt-0" style="z-index: 900">
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ (in_array($path[0], ['clients', 'dashboard']))?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.dashboard')}}">
                            <i class="fas fa-chart-line"></i><span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ ($path[0] === 'plans')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.plans.index')}}">
                            <i class="fas fa-shopping-cart"></i><span>Planes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="">
                        <a class="nav-link" href="{{url('logs')}}" target="_BLANK">
                            <i class="fas fa-bug"></i><span>Logs</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ ($path[0] === 'configurations')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.configuration.index')}}">
                            <i class="fas fa-cogs"></i><span>Configuracion</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ ($path[0] === 'auto-update')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.update')}}">
                            <i class="fas fa-code-branch"></i><span>Actualización</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ ($path[0] === 'accounting')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.accounting.index')}}">
                            <i class="fas fa-calculator"></i><span>Contabilidad</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ ($path[0] === 'backup')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('system.backup')}}">
                            <i class="fas fa-download"></i><span>Backup</span>
                        </a>
                    </li>
                </ul>
            </nav>



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
