<header class="custom-app-header d-flex align-items-center px-4 px-lg-6">
    <div class="d-flex align-items-center justify-content-between w-100">
        
        <!-- Left: Mobile Menu Toggle & Brand -->
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn btn-icon btn-light d-lg-none" id="sidebarMobileToggle" aria-label="Abrir menú">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <a href="{{ route('curso') }}" class="custom-brand">
                <div class="icon-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <span>Curso<strong>+</strong></span>
            </a>

            <!-- Welcome badge on desktop -->
            <div class="d-none d-md-flex align-items-center ms-4 ps-4 border-start border-light-subtle">
                <span class="text-muted fs-7 fw-semibold">
                    <i class="bi bi-person-circle text-primary me-1"></i>
                    Hola, <strong class="text-dark">{{ Auth::user()->name ?? 'Estudiante' }}</strong>
                </span>
            </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="d-flex align-items-center gap-2 gap-md-3">
            <a href="{{ route('curso') }}" class="btn btn-sm btn-light-primary fw-bold d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-compass"></i>
                <span>Explorar Cursos</span>
            </a>

            <a href="{{ route('crearcurso') }}" class="btn btn-sm btn-primary fw-bold d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-plus-circle"></i>
                <span>Nuevo Curso</span>
            </a>

            <!-- User Dropdown Menu -->
            <div class="dropdown">
                <button class="btn btn-icon btn-light rounded-circle shadow-sm p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 0.95rem;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 mt-2" style="border-radius: 14px; min-width: 240px;">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold text-dark fs-6">{{ Auth::user()->name ?? 'Usuario' }}</div>
                        <div class="text-muted fs-7 text-truncate">{{ Auth::user()->email ?? '' }}</div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-gray-700" href="{{ route('vistaperfil') }}">
                            <i class="bi bi-credit-card text-primary fs-5"></i>
                            <span>Mis Tarjetas y Pagos</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2 text-gray-700" href="{{ route('mis-suscripciones') }}">
                            <i class="bi bi-bookmark-check text-success fs-5"></i>
                            <span>Mis Suscripciones</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger">
                                <i class="bi bi-box-arrow-right fs-5"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</header>