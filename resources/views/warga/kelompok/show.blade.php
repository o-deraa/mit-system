@extends('layouts.app', ['title' => 'Kelompok Saya'])

@section('content')
<h1 class="h3 mb-4">Kelompok Saya</h1>

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

@if(!$group)
    <div class="alert alert-warning">
        Kamu belum tergabung dalam kelompok warga. Hubungi admin untuk dimasukkan ke kelompok.
    </div>

    <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
@else
    <div class="card mb-4">
        <div class="card-header">
            Informasi Kelompok
        </div>
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th style="width: 220px">Kode Kelompok</th>
                    <td>Kelompok {{ $group->kode_kelompok }}</td>
                </tr>
                <tr>
                    <th>Status Kelompok</th>
                    <td>{{ $group->status }}</td>
                </tr>
                <tr>
                    <th>Perwakilan</th>
                    <td>
                        {{ $group->representative?->nama }}
                        |
                        {{ $group->representative?->nrp }}
                        |
                        {{ $group->representative?->angkatan }}
                    </td>
                </tr>
                <tr>
                    <th>Nomor WA Perwakilan</th>
                    <td>
                        @if($group->nomor_wa_perwakilan)
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $group->nomor_wa_perwakilan) }}" target="_blank">
                                {{ $group->nomor_wa_perwakilan }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Rules / Catatan</th>
                    <td>{{ $group->rules ?: '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Anggota Kelompok
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Nama</th>
                    <th>NRP</th>
                    <th>Angkatan</th>
                    <th>Role</th>
                </tr>
                </thead>
                <tbody>
                @foreach($group->members as $member)
                    <tr>
                        <td>{{ $member->member_id }}</td>
                        <td>{{ $member->warga?->nama }}</td>
                        <td>{{ $member->warga?->nrp }}</td>
                        <td>{{ $member->warga?->angkatan }}</td>
                        <td>
                            @if($member->warga_id == $group->warga_id)
                                Perwakilan
                            @else
                                Anggota
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('warga.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
@endif
@endsection
