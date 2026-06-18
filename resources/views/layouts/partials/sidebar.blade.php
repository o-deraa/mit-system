<aside class="navbar navbar-vertical navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <h1 class="navbar-brand navbar-brand-autodark">
            <a href="#">
                @php
                    $dashboardUrl = match (session('mit_role')) {
                        'admin' => route('admin.dashboard'),
                        'warga' => route('warga.dashboard'),
                        'maba' => route('maba.dashboard'),
                        default => route('mit.login'),
                    };
                @endphp

                <a href="{{ $dashboardUrl }}">
                    <span class="fw-bold">MIT System</span>
                </a>
            </a>
        </h1>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                @if(session('mit_role') === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <span class="nav-link-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.maba.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-user"></i></span>
                            <span class="nav-link-title">Manajemen Maba</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.warga.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-users"></i></span>
                            <span class="nav-link-title">Manajemen Warga</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.kelompok-warga.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-users-group"></i></span>
                            <span class="nav-link-title">Kelompok Warga</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.mit-week.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-calendar"></i></span>
                            <span class="nav-link-title">Minggu MIT</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.booking.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-calendar-check"></i></span>
                            <span class="nav-link-title">Monitor Booking</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.realisasi.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-clipboard-check"></i></span>
                            <span class="nav-link-title">Monitor Realisasi</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.verification.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-checkup-list"></i></span>
                            <span class="nav-link-title">Verifikasi TTD</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.queue.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-list-check"></i></span>
                            <span class="nav-link-title">Queue Aktif</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.logs.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-database"></i></span>
                            <span class="nav-link-title">Log MongoDB</span>
                        </a>
                    </li>
                @endif

                @if(session('mit_role') === 'warga')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.dashboard') }}">
                            <span class="nav-link-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.availability.edit') }}">
                            <span class="nav-link-icon"><i class="ti ti-calendar-plus"></i></span>
                            <span class="nav-link-title">Ketersediaan</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.booking.incoming') }}">
                            <span class="nav-link-icon"><i class="ti ti-inbox"></i></span>
                            <span class="nav-link-title">Booking Masuk</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.booking.accepted') }}">
                            <span class="nav-link-icon"><i class="ti ti-calendar-check"></i></span>
                            <span class="nav-link-title">Jadwal Accepted</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('warga.booking.history') }}">
                            <span class="nav-link-icon"><i class="ti ti-history"></i></span>
                            <span class="nav-link-title">Riwayat Booking</span>
                        </a>
                    </li>
                @endif

                @if(session('mit_role') === 'maba')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.dashboard') }}">
                            <span class="nav-link-icon"><i class="ti ti-dashboard"></i></span>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.booking.available-groups') }}">
                            <span class="nav-link-icon"><i class="ti ti-search"></i></span>
                            <span class="nav-link-title">Kelompok Tersedia</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.booking.joinable') }}">
                            <span class="nav-link-icon"><i class="ti ti-user-plus"></i></span>
                            <span class="nav-link-title">Gabung Booking</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.booking.mine') }}">
                            <span class="nav-link-icon"><i class="ti ti-calendar-event"></i></span>
                            <span class="nav-link-title">Booking Saya</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.progress.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-chart-bar"></i></span>
                            <span class="nav-link-title">Progress TTD</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.verification.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-checklist"></i></span>
                            <span class="nav-link-title">Status Verifikasi</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.recommendation.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-bulb"></i></span>
                            <span class="nav-link-title">Rekomendasi</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.history.index') }}">
                            <span class="nav-link-icon"><i class="ti ti-history"></i></span>
                            <span class="nav-link-title">Riwayat Kelompok</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('maba.history.check') }}">
                            <span class="nav-link-icon"><i class="ti ti-circle-check"></i></span>
                            <span class="nav-link-title">Cek Kelompok</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</aside>
