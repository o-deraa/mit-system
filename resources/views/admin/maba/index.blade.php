@extends('layouts.app', ['title' => 'Manajemen Maba'])

@section('content')
<x-page-header title="Manajemen Maba" subtitle="Kelola data maba MIT 2025">
    <x-slot:actions>
        <a href="{{ route('admin.maba.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Tambah Maba
        </a>
    </x-slot:actions>
</x-page-header>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-9">
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari nama atau NRP maba">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">
                    <i class="ti ti-search me-1"></i>
                    Cari
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>NRP</th>
                <th>Status</th>
                <th class="w-1">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($mabaList as $maba)
                <tr>
                    <td>{{ $maba->maba_id }}</td>
                    <td class="fw-semibold">{{ $maba->nama }}</td>
                    <td>{{ $maba->nrp }}</td>
                    <td>
                        @if($maba->status === 'active')
                            <span class="badge bg-green-lt text-green">active</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.maba.edit', $maba->maba_id) }}" class="btn btn-sm btn-warning">
                                <i class="ti ti-edit"></i>
                            </a>

                            <form action="{{ route('admin.maba.destroy', $maba->maba_id) }}" method="POST"
                                  onsubmit="return confirm('Hapus maba ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="Belum ada data maba" message="Tambahkan maba baru untuk mulai mengelola data." />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-end">
        {{ $mabaList->links() }}
    </div>
</div>
@endsection
