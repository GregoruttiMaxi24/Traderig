<nav class="navbar navbar-expand-lg tr-navbar" data-bs-theme="dark">
    <div class="container">
        {{-- Isologotipo vertical monocromático: el que el manual de marca indica sobre fondo oscuro --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/logo-full-light.png') }}" alt="TRADERIG" height="52">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-lg-auto gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#categorias">Categorías</a>
                </li>
                {{-- Estos enlaces aparecen solos cuando se creen las rutas en las próximas fases --}}
                @if (Route::has('listings.index'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('listings.index') ? 'active' : '' }}" href="{{ route('listings.index') }}">Catálogo</a>
                    </li>
                @endif
                @if (Route::has('auctions.index'))
                    <li class="nav-item"><a class="nav-link" href="{{ route('auctions.index') }}">Subastas</a></li>
                @endif
                @auth
                    @if (Route::has('dashboard'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Mi panel</a></li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-tr dropdown-toggle d-flex align-items-center gap-2" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <x-avatar :user="auth()->user()" size="sm" />
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mi perfil</a></li>
                            <li><a class="dropdown-item" href="{{ route('users.show', auth()->user()) }}">Mi página pública</a></li>
                            @if (auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.index') }}">Administración</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="nav-link px-2" href="{{ route('login') }}">Ingresar</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
