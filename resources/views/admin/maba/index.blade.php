@extends('layouts.app', ['title' => 'Manajemen Maba'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Manajemen Maba</h1>
    <a href="{{ route('admin.maba.create') }}" class="btn btn-dark">Tambah Maba</a>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-8">
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama / NRP">
    </div>
    <div class="col-md-4">
        <button class="btn btn-outline-dark">Cari</button>
        <a href="{{ route('admin.maba.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                <th>Status</th>
                <th style="width: 180px">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($mabaList as $maba)
                <tr>
                    <td>{{ $maba->maba_id }}</td>
                    <td>{{ $maba->nama }}</td>
                    <td>{{ $maba->nrp }}</td>
                    <td>{{ $maba->status }}</td>
                    <td>
                        <a href="{{ route('admin.maba.edit', $maba->maba_id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.maba.destroy', $maba->maba_id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus maba ini? Data yang sudah punya relasi tidak akan bisa dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada data maba.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $mabaList->links() }}
    </div>
</div>
@endsection
