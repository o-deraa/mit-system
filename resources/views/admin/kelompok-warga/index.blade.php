@extends('layouts.app', ['title' => 'Manajemen Kelompok Warga'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Manajemen Kelompok Warga</h1>
    <a href="{{ route('admin.kelompok-warga.create') }}" class="btn btn-dark">Bentuk Kelompok Warga</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Perwakilan</th>
                <th>NRP</th>
                <th>Angkatan</th>
                <th>WA</th>
                <th>Anggota</th>
                <th>Status</th>
                <th style="width: 220px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>{{ $group->kelompok_warga_id }}</td>
                    <td>Kelompok {{ $group->kode_kelompok }}</td>
                    <td>{{ $group->representative?->nama }}</td>
                    <td>{{ $group->representative?->nrp }}</td>
                    <td>{{ $group->representative?->angkatan }}</td>
                    <td>{{ $group->nomor_wa_perwakilan }}</td>
                    <td>{{ $group->members->count() }} / 4</td>
                    <td>{{ $group->status }}</td>
                    <td>
                        <a href="{{ route('admin.kelompok-warga.show', $group->kelompok_warga_id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('admin.kelompok-warga.edit', $group->kelompok_warga_id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.kelompok-warga.destroy', $group->kelompok_warga_id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus kelompok ini? Kelompok yang sudah dipakai booking tidak bisa dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Belum ada kelompok warga.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $groups->links() }}
    </div>
</div>
@endsection
