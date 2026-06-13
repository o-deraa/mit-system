
@extends('layouts.app', ['title' => 'Dashboard Warga'])

@section('content')
<h1 class="h3 mb-4">Dashboard Warga</h1>

<div class="card mb-4">
    <div class="card-header">
        Profil Warga
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width: 200px">Nama</th>
                <td>{{ $warga->nama }}</td>
            </tr>
            <tr>
                <th>NRP</th>
                <td>{{ $warga->nrp }}</td>
            </tr>
            <tr>
                <th>Angkatan</th>
                <td>{{ $warga->angkatan }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $warga->status }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Menu Warga Tahap Berikutnya
    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="{{ route('warga.kelompok.show') }}" class="list-group-item list-group-item-action">Lihat Kelompok Saya</a>
            <a href="{{ route('warga.availability.edit') }}" class="list-group-item list-group-item-action">Input Ketersediaan Mingguan</a>
            <a href="{{ route('warga.booking.incoming') }}" class="list-group-item list-group-item-action">Lihat Booking Masuk</a>
            <a href="{{ route('warga.booking.accepted') }}" class="list-group-item list-group-item-action">Lihat Jadwal Accepted</a>
            <a href="{{ route('warga.booking.history') }}" class="list-group-item list-group-item-action">Lihat Riwayat Booking</a>
        </div>
    </div>
</div>
@endsection
