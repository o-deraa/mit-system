<header class="navbar navbar-expand-md d-print-none bg-white border-bottom">
    <div class="container-xl">
        <div class="navbar-nav flex-row order-md-last ms-auto">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                    <span class="avatar avatar-sm">
                        {{ strtoupper(substr(session('mit_user_name', 'U'), 0, 1)) }}
                    </span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ session('mit_user_name') }}</div>
                        <div class="mt-1 small text-muted">{{ strtoupper(session('mit_role')) }}</div>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <form action="{{ route('mit.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ti ti-logout me-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="text-muted small">MIT Departemen Teknologi Informasi</div>
        </div>
    </div>
</header>
