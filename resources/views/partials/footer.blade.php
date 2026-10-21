<footer class="tr-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <img src="{{ asset('img/logo-full-light.png') }}" alt="TRADERIG" height="64">
                <p class="mt-3 mb-0 tr-footer__text">
                    Donde el hardware encuentra su próximo destino. Comprá, intercambiá o subastá componentes de PC usados con una comunidad que sabe de hardware.
                </p>
            </div>
            <div class="col-6 col-lg-3 offset-lg-1">
                <h2 class="h5">Marketplace</h2>
                <ul class="list-unstyled tr-links">
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('home') }}#categorias">Categorías</a></li>
                    @if (Route::has('listings.index'))
                        <li><a href="{{ route('listings.index') }}">Catálogo</a></li>
                    @endif
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h2 class="h5">Tu cuenta</h2>
                <ul class="list-unstyled tr-links">
                    @auth
                        <li><a href="{{ route('profile.edit') }}">Mi perfil</a></li>
                        <li><a href="{{ route('users.show', auth()->user()) }}">Mi página pública</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Ingresar</a></li>
                        <li><a href="{{ route('register') }}">Crear cuenta</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <p class="tr-meta tr-footer__text mt-4 mb-0">www.traderig.com.ar · Proyecto final {{ date('Y') }}</p>
    </div>
</footer>
