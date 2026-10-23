<aside class="custom-sidebar" id="customAppSidebar">
    <div>
        <!-- Section: Menú Principal -->
        <div class="sidebar-nav-header">
            Plataforma
        </div>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('curso') }}" class="sidebar-nav-link {{ request()->routeIs('curso') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Explorar Cursos</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('mis-suscripciones') }}" class="sidebar-nav-link {{ request()->routeIs('mis-suscripciones') ? 'active' : '' }}">
                    <i class="bi bi-collection-play-fill"></i>
                    <span>Mis Suscripciones</span>
                </a>
            </li>
        </ul>

        <!-- Section: Gestión -->
        <div class="sidebar-nav-header">
            Gestión Académica
        </div>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('crearcurso') }}" class="sidebar-nav-link {{ request()->routeIs('crearcurso') ? 'active' : '' }}">
                    <i class="bi bi-journal-plus"></i>
                    <span>Crear Curso</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('maestros') }}" class="sidebar-nav-link {{ request()->routeIs('maestros') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Registrar Maestros</span>
                </a>
            </li>
        </ul>

        <!-- Section: Finanzas / Pagos -->
        <div class="sidebar-nav-header">
            Mi Cuenta
        </div>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('vistaperfil') }}" class="sidebar-nav-link {{ request()->routeIs('vistaperfil') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-back-fill"></i>
                    <span>Tarjetas de Pago</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Bottom / User Footer -->
    <div class="sidebar-user-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="user-mini-card">
                <div class="user-avatar-circle">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden" style="max-width: 120px;">
                    <div class="text-white fw-bold fs-7 text-truncate">{{ Auth::user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted fs-8 text-truncate">{{ '@' . (Auth::user()->usuario ?? 'cuenta') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn-sidebar-logout" title="Cerrar sesión">
                    <i class="bi bi-power"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
