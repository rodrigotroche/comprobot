<header class="p-3 mb-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap">
                    <use xlink:href="#bootstrap"></use>
                </svg>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li>
                    <a href="{{ route('frontend.index') }}"
                        class="nav-link px-2 {{ request()->routeIs('frontend.index') ? 'link-body-emphasis' : 'link-secondary' }}">Inicio</a>
                </li>
                <li>
                    <a href="{{ route('frontend.search') }}"
                        class="nav-link px-2 {{ request()->routeIs('frontend.search') ? 'link-body-emphasis' : 'link-secondary' }}">Explorar</a>
                </li>
            </ul>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search" method="GET" action="{{ route('frontend.search') }}">
                <input type="search" name="search" class="form-control" placeholder="Buscar..." aria-label="Search">
            </form>
            @if (Route::has('login'))
            <div class="text-end">
                @auth
                <div class="dropdown text-end">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li>
                            <a class="dropdown-item" href="{{ route('frontend.shopping-lists.index') }}">Listas</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">Perfil</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    Salir
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Iniciar sesión</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                @endif
                @endauth
            </div>
            @endif

        </div>
    </div>
</header>