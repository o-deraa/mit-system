@extends('layouts.app', ['title' => 'Detail Kelompok Warga'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Detail Kelompok {{ $group->kode_kelompok }}</h1>
    <div>
        <a href="{{ route('admin.kelompok-warga.edit', $group->kelompok_warga_id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.kelompok-warga.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Informasi Kelompok</div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width: 220px">Kode Kelompok</th>
                <td>{{ $group->kode_kelompok }}</td>
            </tr>
            <tr>
                <th>Perwakilan</th>
                <td>{{ $group->representative?->nama }} | {{ $group->representative?->nrp }} | {{ $group->representative?->angkatan }}</td>
            </tr>
            <tr>
                <th>Nomor WA</th>
                <td>{{ $group->nomor_wa_perwakilan }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $group->status }}</td>
            </tr>
            <tr>
                <th>Rules</th>
                <td>{{ $group->rules ?: '-' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Anggota Kelompok</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Member ID</th>
                <th>Nama</th>
                <th>NRP</th>
                <th>Angkatan</th>
                <th>Role</th>
                <th style="width: 120px">Aksi</th>
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
                    <td>
                        @if($member->warga_id != $group->warga_id)
                            <form action="{{ route('admin.kelompok-warga.members.destroy', [$group->kelompok_warga_id, $member->member_id]) }}" method="POST"
                                  onsubmit="return confirm('Kurangi anggota ini dari kelompok?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Kurangi</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($group->members->count() < 4)
    <div class="card">
        <div class="card-header">Tambah Anggota</div>
        <div class="card-body">
            <form action="{{ route('admin.kelompok-warga.members.store', $group->kelompok_warga_id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Warga Floating</label>
                    <select name="warga_id" class="form-select" required>
                        <option value="">-- Pilih Warga --</option>
                        @foreach($floatingWarga as $warga)
                            <option value="{{ $warga->warga_id }}">
                                {{ $warga->nama }} | {{ $warga->nrp }} | {{ $warga->angkatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-dark">Tambah Anggota</button>
            </form>
        </div>
    </div>
@endif
@endsection
