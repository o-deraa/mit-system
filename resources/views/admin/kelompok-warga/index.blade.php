@extends('layouts.app', ['title' => 'Kelompok Warga'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Kelompok Warga</h1>
    <a href="{{ route('admin.kelompok-warga.create') }}" class="btn btn-dark">Tambah</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Kode</th>
                <th>Status</th>
                <th>Jumlah Anggota</th>
                <th>Perwakilan</th>
                <th>WA Perwakilan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>Kelompok {{ $group->kode_kelompok }}</td>
                    <td>{{ $group->status }}</td>
                    <td>{{ $group->members->count() }}</td>
                    <td>{{ $group->representativeMember?->warga?->nama ?? '-' }}</td>
                    <td>{{ $group->representativeMember?->nomor_wa ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.kelompok-warga.show', $group->kelompok_warga_id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('admin.kelompok-warga.edit', $group->kelompok_warga_id) }}" class="btn btn-sm btn-warning">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada kelompok warga.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $groups->links() }}
    </div>
</div>
@endsection
