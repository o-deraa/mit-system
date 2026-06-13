@extends('layouts.app', ['title' => 'Dashboard Admin'])

@section('content')
<h1 class="h3 mb-4">Dashboard Admin</h1>

@if($activeWeek)
    <div class="alert alert-success">
        Minggu MIT aktif: <strong>Minggu {{ $activeWeek->week_number }}</strong>
        — {{ $activeWeek->start_date }} s.d. {{ $activeWeek->end_date }}
    </div>
@else
    <div class="alert alert-warning">
        Belum ada minggu MIT yang aktif.
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Maba</div>
                <div class="fs-3 fw-bold">{{ $totalMaba }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Warga</div>
                <div class="fs-3 fw-bold">{{ $totalWarga }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Kelompok Warga</div>
                <div class="fs-3 fw-bold">{{ $totalKelompok }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Minggu MIT</div>
                <div class="fs-3 fw-bold">{{ $totalWeek }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Booking</div>
                <div class="fs-3 fw-bold">{{ $totalBooking }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">Total Realisasi</div>
                <div class="fs-3 fw-bold">{{ $totalRealisasi }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Menu Admin
    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="{{ route('admin.maba.index') }}" class="list-group-item list-group-item-action">Manajemen Maba</a>
            <a href="{{ route('admin.warga.index') }}" class="list-group-item list-group-item-action">Manajemen Warga</a>
            <a href="{{ route('admin.kelompok-warga.index') }}" class="list-group-item list-group-item-action">Manajemen Kelompok Warga</a>
            <a href="{{ route('admin.mit-week.index') }}" class="list-group-item list-group-item-action">Manajemen Minggu MIT</a>
            <a href="{{ route('admin.realisasi.index') }}" class="list-group-item list-group-item-action">Monitor Realisasi</a>
            <a href="{{ route('admin.verification.index') }}" class="list-group-item list-group-item-action">Verifikasi TTD</a>
            <a href="{{ route('admin.booking.index') }}" class="list-group-item list-group-item-action">Monitor Booking</a>
            <a href="{{ route('admin.queue.index') }}" class="list-group-item list-group-item-action">Monitoring Queue Aktif</a>
            <a href="{{ route('admin.logs.index') }}" class="list-group-item list-group-item-action">Log MongoDB</a>
        </div>
    </div>
</div>
@endsection
