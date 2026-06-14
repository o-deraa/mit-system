@extends('layouts.app', ['title' => 'Tambah Maba'])

@section('content')
<x-page-header title="Tambah Maba" subtitle="Tambah data maba MIT 2025" />

<div class="card">
    <div class="card-header">
        Form Tambah Maba
    </div>

    <div class="card-body">
        <form action="{{ route('admin.maba.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text"
                       name="nama"
                       value="{{ old('nama') }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">NRP</label>
                <input type="text"
                       name="nrp"
                       value="{{ old('nrp') }}"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="text"
                       name="password"
                       value="{{ old('password') }}"
                       class="form-control">
                <div class="form-hint">
                    Kosongkan jika ingin password default mengikuti NRP.
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="active" @selected(old('status', 'active') === 'active')>active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>inactive</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>
                    Simpan
                </button>

                <a href="{{ route('admin.maba.index') }}" class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
