<nav class="navbar navbar-expand-md tr-navbar" data-bs-theme="dark">
    <div class="container">
        {{-- Sobre fondo oscuro el manual de marca pide la versión monocromática del logo --}}
        <a class="navbar-brand py-0" href="{{ route('home') }}">
            <img src="{{ asset('img/logo-icon-light.png') }}" alt="TRADERIG" height="38">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                </li>
                {{-- Los enlaces aparecen solos cuando se creen las rutas en las próximas fases --}}
                @if (Route::has('listings.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('listings.*') ? 'active' : '' }}" href="{{ route('listings.index') }}">Catálogo</a>
                    </li>
                @endif
                @auth
                    @if (Route::has('dashboard'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Mi panel</a></li>
                    @endif
                    @if (auth()->user()->isAdmin() && Route::has('admin.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.index') }}">Administración</a>
                        </li>
                    @endif
                @endauth
            </ul>

            <ul class="navbar-nav align-items-md-center gap-md-2">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Ingresar</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm" href="{{ route('register') }}">Crear cuenta</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
