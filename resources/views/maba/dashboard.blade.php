@extends('layouts.app', ['title' => 'Dashboard Maba'])

@section('content')
<h1 class="h3 mb-4">Dashboard Maba</h1>

<div class="card mb-4">
    <div class="card-header">
        Profil Maba
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width: 200px">Nama</th>
                <td>{{ $maba->nama }}</td>
            </tr>
            <tr>
                <th>NRP</th>
                <td>{{ $maba->nrp }}</td>
            </tr>
            <tr>
                <th>Angkatan</th>
                <td>2025</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $maba->status }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Target TTD
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th>TTD Warga 2022</th>
                <td>{{ $target['2022'] }}</td>
            </tr>
            <tr>
                <th>TTD Warga 2023</th>
                <td>{{ $target['2023'] }}</td>
            </tr>
            <tr>
                <th>TTD Warga 2024</th>
                <td>{{ $target['2024'] }}</td>
            </tr>
            <tr>
                <th>Total Target</th>
                <td>{{ $target['total'] }}</td>
            </tr>
            <tr>
                <th>Minimal Mingguan</th>
                <td>{{ $target['minimum_weekly'] }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Menu Maba Tahap Berikutnya
    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="{{ route('maba.progress.index') }}" class="list-group-item list-group-item-action">Lihat Progress TTD</a>
            <a href="{{ route('maba.booking.available-groups') }}" class="list-group-item list-group-item-action">Lihat Kelompok Warga Tersedia</a>
            <a href="{{ route('maba.booking.mine') }}" class="list-group-item list-group-item-action">Lihat Booking Saya</a>
            <a href="{{ route('maba.verification.index') }}" class="list-group-item list-group-item-action">Lihat Status Verifikasi</a>
            <a href="{{ route('maba.recommendation.index') }}" class="list-group-item list-group-item-action">Rekomendasi Kelompok</a>
            <a href="{{ route('maba.history.index') }}" class="list-group-item list-group-item-action">Riwayat Kelompok Warga</a>
            <a href="{{ route('maba.history.check') }}" class="list-group-item list-group-item-action">Cek Pernah Bertemu Kelompok</a>
        </div>
    </div>
</div>
@endsection
