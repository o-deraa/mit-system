@extends('layouts.app', ['title' => 'Dashboard Admin'])

@section('content')
<x-page-header title="Dashboard Admin" subtitle="Ringkasan sistem MIT Departemen Teknologi Informasi" />

@if($activeWeek)
    <div class="alert alert-success">
        <i class="ti ti-calendar-check me-2"></i>
        Minggu MIT aktif: <strong>Minggu {{ $activeWeek->week_number }}</strong>
        — {{ $activeWeek->start_date }} s.d. {{ $activeWeek->end_date }}
    </div>
@else
    <div class="alert alert-warning">
        <i class="ti ti-alert-triangle me-2"></i>
        Belum ada minggu MIT yang aktif.
    </div>
@endif

<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-user"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Total Maba</div>
                        <div class="h2 mb-0">{{ $totalMaba }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-users"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Total Warga</div>
                        <div class="h2 mb-0">{{ $totalWarga }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-users-group"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Kelompok Warga</div>
                        <div class="h2 mb-0">{{ $totalKelompok }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-calendar"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Minggu MIT</div>
                        <div class="h2 mb-0">{{ $totalWeek }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-clipboard-list"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Booking</div>
                        <div class="h2 mb-0">{{ $totalBooking }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card mit-stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="mit-stat-icon"><i class="ti ti-report"></i></span>
                    <div class="ms-3">
                        <div class="text-muted">Realisasi</div>
                        <div class="h2 mb-0">{{ $totalRealisasi }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Cepat Admin</h3>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.maba.index') }}" class="list-group-item list-group-item-action">
            <i class="ti ti-user me-2"></i> Manajemen Maba
        </a>
        <a href="{{ route('admin.warga.index') }}" class="list-group-item list-group-item-action">
            <i class="ti ti-users me-2"></i> Manajemen Warga
        </a>
        <a href="{{ route('admin.booking.index') }}" class="list-group-item list-group-item-action">
            <i class="ti ti-clipboard-list me-2"></i> Monitor Booking
        </a>
        <a href="{{ route('admin.verification.index') }}" class="list-group-item list-group-item-action">
            <i class="ti ti-checkup-list me-2"></i> Verifikasi TTD
        </a>
    </div>
</div>
@endsection
