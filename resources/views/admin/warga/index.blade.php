@extends('layouts.app', ['title' => 'Manajemen Warga'])

@section('content')
<x-page-header title="Manajemen Warga" subtitle="Kelola warga angkatan 2022, 2023, dan 2024">
    <x-slot:actions>
        <a href="{{ route('admin.warga.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>
            Tambah Warga
        </a>
    </x-slot:actions>
</x-page-header>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    class="form-control"
                    placeholder="Cari nama atau NRP warga"
                >
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
                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill">
                        <i class="ti ti-search me-1"></i>
                        Cari
                    </button>

                    <a href="{{ route('admin.warga.index') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
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
                <th>Angkatan</th>
                <th>Kelompok</th>
                <th>Status</th>
                <th class="w-1">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse($wargaList as $warga)
                <tr>
                    <td>{{ $warga->warga_id }}</td>

                    <td class="fw-semibold">
                        {{ $warga->nama }}
                    </td>

                    <td>{{ $warga->nrp }}</td>

                    <td>{{ $warga->angkatan }}</td>

                    <td>
                        @if($warga->membership?->group)
                            Kelompok {{ $warga->membership->group->kode_kelompok }}
                        @else
                            <span class="text-muted">Floating</span>
                        @endif
                    </td>

                    <td>
                        @if($warga->status === 'active')
                            <span class="badge bg-green-lt text-green">active</span>
                        @else
                            <span class="badge bg-secondary-lt text-secondary">inactive</span>
                        @endif
                    </td>

                    <td>
                        <div class="btn-list flex-nowrap">
                            <a href="{{ route('admin.warga.edit', $warga->warga_id) }}" class="btn btn-sm btn-warning">
                                <i class="ti ti-edit"></i>
                            </a>

                            <form
                                action="{{ route('admin.warga.destroy', $warga->warga_id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus warga ini? Data yang sudah punya relasi tidak akan bisa dihapus.')"
                            >
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
                    <td colspan="7">
                        <x-empty-state title="Belum ada data warga" message="Tambahkan warga baru untuk mulai mengelola data." />
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <div class="pagination-left">
            {{ $wargaList->links() }}
        </div>
</div>
</div>
@endsection
