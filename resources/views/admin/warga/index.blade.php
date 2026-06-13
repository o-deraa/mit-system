@extends('layouts.app', ['title' => 'Manajemen Warga'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Manajemen Warga</h1>
    <a href="{{ route('admin.warga.create') }}" class="btn btn-dark">Tambah Warga</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama / NRP">
    </div>
    <div class="col-md-3">
        <select name="angkatan" class="form-select">
            <option value="">Semua Angkatan</option>
            <option value="2022" @selected($angkatan == '2022')>2022</option>
            <option value="2023" @selected($angkatan == '2023')>2023</option>
            <option value="2024" @selected($angkatan == '2024')>2024</option>
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-dark">Filter</button>
        <a href="{{ route('admin.warga.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NRP</th>
                <th>Angkatan</th>
                <th>Kelompok</th>
                <th>Status</th>
                <th style="width: 180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($wargaList as $warga)
                <tr>
                    <td>{{ $warga->warga_id }}</td>
                    <td>{{ $warga->nama }}</td>
                    <td>{{ $warga->nrp }}</td>
                    <td>{{ $warga->angkatan }}</td>
                    <td>
                        @if($warga->membership?->group)
                            Kelompok {{ $warga->membership->group->kode_kelompok }}
                        @else
                            <span class="text-muted">Floating</span>
                        @endif
                    </td>
                    <td>{{ $warga->status }}</td>
                    <td>
                        <a href="{{ route('admin.warga.edit', $warga->warga_id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.warga.destroy', $warga->warga_id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus warga ini? Data yang sudah punya relasi tidak akan bisa dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data warga.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $wargaList->links() }}
    </div>
</div>
@endsection
