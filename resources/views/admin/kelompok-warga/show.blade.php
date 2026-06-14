@extends('layouts.app', ['title' => 'Detail Kelompok Warga'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Detail Kelompok {{ $group->kode_kelompok }}</h1>
    <a href="{{ route('admin.kelompok-warga.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card mb-4">
    <div class="card-header">Informasi Kelompok</div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Kode Kelompok</th>
                <td>Kelompok {{ $group->kode_kelompok }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $group->status }}</td>
            </tr>
            <tr>
                <th>Rules</th>
                <td>{{ $group->rules ?: '-' }}</td>
            </tr>
            <tr>
                <th>Perwakilan</th>
                <td>{{ $group->representativeMember?->warga?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <th>WA Perwakilan</th>
                <td>{{ $group->representativeMember?->nomor_wa ?? '-' }}</td>
            </tr>
        </table>

        <form action="{{ route('admin.kelompok-warga.finalize', $group->kelompok_warga_id) }}" method="POST">
            @csrf
            <button class="btn btn-success">Finalisasi Kelompok</button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Tambah Anggota</div>
    <div class="card-body">
        <form action="{{ route('admin.kelompok-warga.members.store', $group->kelompok_warga_id) }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Warga</label>
                    <select name="warga_id" class="form-select" required>
                        <option value="">-- Pilih Warga --</option>
                        @foreach($availableWarga as $warga)
                            <option value="{{ $warga->warga_id }}">
                                {{ $warga->nama }} | {{ $warga->nrp }} | {{ $warga->angkatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jadikan Perwakilan?</label>
                    <select name="is_perwakilan" class="form-select">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nomor WA</label>
                    <input type="text" name="nomor_wa" class="form-control" placeholder="Wajib jika perwakilan">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-dark w-100">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Anggota Kelompok</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
            <tr>
                <th>Nama</th>
                <th>NRP</th>
                <th>Angkatan</th>
                <th>Perwakilan</th>
                <th>Nomor WA</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($group->members as $member)
                <tr>
                    <td>{{ $member->warga?->nama }}</td>
                    <td>{{ $member->warga?->nrp }}</td>
                    <td>{{ $member->warga?->angkatan }}</td>
                    <td>
                        @if($member->is_perwakilan)
                            <span class="badge bg-success">Perwakilan</span>
                        @else
                            <span class="badge bg-secondary">Anggota</span>
                        @endif
                    </td>
                    <td>{{ $member->nomor_wa ?: '-' }}</td>
                    <td>
                        @if(!$member->is_perwakilan)
                            <form action="{{ route('admin.kelompok-warga.members.representative', $member->member_id) }}"
                                  method="POST"
                                  class="d-inline-block mb-1">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" name="nomor_wa" class="form-control" placeholder="Nomor WA" required>
                                    <button class="btn btn-success">Jadikan Perwakilan</button>
                                </div>
                            </form>
                        @endif

                        <form action="{{ route('admin.kelompok-warga.members.destroy', $member->member_id) }}"
                              method="POST"
                              class="d-inline-block"
                              onsubmit="return confirm('Hapus anggota ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada anggota.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
